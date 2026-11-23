<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('innovations.show', $innovation) }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold uppercase tracking-wider flex items-center mb-1">
                    ← Back to Innovation Details
                </a>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Edit Innovation') }}
                </h2>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $innovation->status->badgeClass() }}">
                {{ $innovation->status->label() }}
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Success Message -->
            @if (session('status'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-md shadow-sm">
                    <span class="text-sm font-medium">{{ session('status') }}</span>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 sm:p-8">

                    <form method="POST" action="{{ route('innovations.update', $innovation) }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Title -->
                        <div>
                            <x-input-label for="title" :value="__('Innovation Title *')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $innovation->title)" required autofocus />
                            <x-input-error class="mt-1" :messages="$errors->get('title')" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Detailed Proposal & Objectives *')" />
                            <textarea id="description" name="description" rows="7" class="mt-1 block w-full border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-md shadow-xs text-sm" required>{{ old('description', $innovation->description) }}</textarea>
                            <x-input-error class="mt-1" :messages="$errors->get('description')" />
                        </div>

                        <!-- Existing Attachments -->
                        <div class="pt-4 border-t border-gray-100">
                            <h4 class="text-sm font-semibold text-gray-800 mb-2">Existing Attachments ({{ $innovation->attachments->count() }})</h4>
                            @if ($innovation->attachments->isEmpty())
                                <p class="text-xs text-gray-400 italic">No attachments uploaded yet.</p>
                            @else
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                                    @foreach ($innovation->attachments as $att)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                            <div class="flex items-center space-x-2 truncate">
                                                <div class="w-8 h-8 rounded bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                                    @if ($att->isImage())
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    @elseif (str_contains($att->mime_type, 'pdf'))
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="truncate text-xs">
                                                    <div class="font-medium text-gray-700 truncate">{{ $att->original_name }}</div>
                                                    <div class="text-gray-400">{{ $att->formattedSize() }}</div>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2 shrink-0 ms-2">
                                                <button type="button" onclick="previewAttachment('{{ $att->url() }}', '{{ $att->original_name }}', '{{ $att->mime_type }}')" class="text-emerald-600 hover:text-emerald-800 p-1" title="Preview">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                                <button type="button"
                                                        onclick="if(confirm('Delete attachment {{ $att->original_name }}?')) document.getElementById('delete-att-{{ $att->id }}').submit();"
                                                        class="text-rose-500 hover:text-rose-700 p-1" title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Upload Additional Attachments -->
                            <x-input-label for="attachments" :value="__('Add More Attachments (Optional)')" />
                            <div class="mt-2 border-2 border-dashed border-gray-200 rounded-xl p-4 text-center bg-gray-50/50">
                                <input id="attachments" name="attachments[]" type="file" multiple class="block w-full text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer" />
                                <p class="text-xs text-gray-400 mt-1">Accepted: PDF, Word, Excel, PPT, Images, Videos (MP4, MOV, AVI, WebM), Audio (MP3, WAV), ZIP (Max 50MB each)</p>
                            </div>
                            @if ($errors->has('attachments'))
                                <div class="mt-2 text-sm text-red-600 space-y-1">
                                    @foreach ($errors->get('attachments') as $error)
                                        <div>{{ $error }}</div>
                                    @endforeach
                                </div>
                            @endif
                            @if ($errors->has('attachments.*'))
                                <div class="mt-2 text-sm text-red-600 space-y-1">
                                    @foreach ($errors->get('attachments.*') as $error)
                                        <div>{{ is_array($error) ? implode(', ', $error) : $error }}</div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Collaborators Section (Alpine.js) -->
                        <div class="pt-4 border-t border-gray-100" x-data="{
                            rows: {{ Js::from($innovation->collaborators->map(fn($c) => ['id' => $c->id, 'user_id' => (string)$c->id, 'role' => $c->pivot->role_in_innovation])) }},
                            addRow() {
                                this.rows.push({ id: Date.now(), user_id: '', role: 'Co-Author' });
                            },
                            removeRow(index) {
                                this.rows.splice(index, 1);
                            }
                        }">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-800">Co-Innovators & Collaborators</h4>
                                    <p class="text-xs text-gray-500">Manage colleagues who can view and edit this innovation.</p>
                                </div>
                                <button type="button" @click="addRow()" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                                    + Add Collaborator
                                </button>
                            </div>

                            <template x-for="(row, index) in rows" :key="row.id">
                                <div class="flex items-center gap-3 mb-2 p-3 bg-gray-50 rounded-lg border border-gray-100">
                                    <div class="flex-1">
                                        <select :name="'collaborators[' + index + '][user_id]'" x-model="row.user_id" class="w-full text-xs rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" required>
                                            <option value="">-- Select Colleague --</option>
                                            @foreach($potentialCollaborators as $collab)
                                                <option value="{{ $collab->id }}">
                                                    {{ $collab->name }} ({{ $collab->department ?? $collab->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="w-1/3">
                                        <input type="text" :name="'collaborators[' + index + '][role]'" x-model="row.role" placeholder="Role (e.g. Co-Author)" class="w-full text-xs rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" />
                                    </div>
                                    <button type="button" @click="removeRow(index)" class="text-rose-500 hover:text-rose-700 p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </template>

                            <div x-show="rows.length === 0" class="text-xs text-gray-400 italic py-2">
                                No collaborators added.
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="pt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                            <a href="{{ route('innovations.show', $innovation) }}" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:text-gray-800 transition">
                                Cancel
                            </a>
                            <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                                Update Innovation
                            </button>
                        </div>
                    </form>

                    <!-- Hidden Delete Attachment Forms -->
                    @foreach ($innovation->attachments as $att)
                        <form id="delete-att-{{ $att->id }}" method="POST" action="{{ route('innovations.attachments.destroy', [$innovation, $att]) }}" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
