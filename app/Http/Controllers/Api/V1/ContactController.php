<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexContactRequest;
use App\Http\Requests\Api\V1\StoreContactRequest;
use App\Http\Requests\Api\V1\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    public function index(IndexContactRequest $request)
    {
        $validated = $request->validated();
        $perPage = $validated['per_page'] ?? 20;

        $query = Contact::with(['category', 'tags']);

        if (! empty($validated['keyword'])) {
            $keyword = $validated['keyword'];

            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%")
                    ->orWhereRaw(
                        'CONCAT(last_name, first_name) LIKE ?',
                        ["%{$keyword}%"]
                    )
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        if (! empty($validated['gender'])) {
            $query->where('gender', $validated['gender']);
        }

        if (! empty($validated['category_id'])) {
            $query->where('category_id', $validated['category_id']);
        }

        if (! empty($validated['date'])) {
            $query->whereDate('created_at', $validated['date']);
        }

        $contacts = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage);

        return ContactResource::collection($contacts);
    }

    public function show(Contact $contact)
    {
        $contact->load(['category', 'tags']);

        return new ContactResource($contact);
    }

    public function store(StoreContactRequest $request)
    {
        $validated = $request->validated();
        $tagIds = $validated['tag_ids'] ?? [];

        unset($validated['tag_ids']);

        $contact = DB::transaction(function () use ($validated, $tagIds) {
            $contact = Contact::create($validated);
            $contact->tags()->sync($tagIds);

            return $contact;
        });

        $contact->load(['category', 'tags']);

        return (new ContactResource($contact))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateContactRequest $request, Contact $contact)
    {
        $validated = $request->validated();
        $tagIds = $validated['tag_ids'] ?? [];

        unset($validated['tag_ids']);

        DB::transaction(function () use ($contact, $validated, $tagIds) {
            $contact->update($validated);
            $contact->tags()->sync($tagIds);
        });

        $contact->load(['category', 'tags']);

        return new ContactResource($contact);
    }

    public function destroy(Contact $contact)
    {
        DB::transaction(function () use ($contact) {
            $contact->tags()->detach();
            $contact->delete();
        });

        return response()->noContent();
    }
}
