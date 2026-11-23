<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('innovations.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold uppercase tracking-wider flex items-center mb-1">
                    ← Back to Innovations
                </a>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Submit New Innovation') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 sm:p-8">

                    <!-- Information banner -->
                    <div class="mb-6 p-4 bg-emerald-50 rounded-lg border border-emerald-100 text-xs text-emerald-800 leading-relaxed">
                        <strong>KSG Innovation Guidelines:</strong> Document your public service innovation idea clearly. It will be saved as a <em>Draft</em> where you can attach supporting documents and add collaborators before submitting it for review.
                    </div>

                    <form method="POST" action="{{ route('innovations.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- Innovation Title -->
                        <div>
                            <x-input-label for="title" :value="__('Innovation Title *')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" placeholder="e.g. AI-Assisted Devolved Health Resource Allocation Model" required autofocus />
                            <p class="text-xs text-gray-400 mt-1">A concise, descriptive name for your transformation idea.</p>
                            <x-input-error class="mt-1" :messages="$errors->get('title')" />
                        </div>

                        <!-- Innovation Description -->
                        <div>
                            <x-input-label for="description" :value="__('Detailed Proposal & Objectives *')" />
                            <textarea id="description" name="description" rows="7" class="mt-1 block w-full border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-md shadow-xs text-sm" placeholder="Provide background on the challenge, your innovative solution, the targeted KSG/Government department, and anticipated institutional outcomes..." required>{{ old('description') }}</textarea>
                            <p class="text-xs text-gray-400 mt-1">Minimum 20 characters.</p>
                            <x-input-error class="mt-1" :messages="$errors->get('description')" />
                        </div>

                        <!-- File Attachments -->
                        <div class="pt-4 border-t border-gray-100">
                            <x-input-label for="attachments" :value="__('Supporting Attachments (Optional, up to 5 files)')" />
                            <div class="mt-2 border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-emerald-400 transition bg-gray-50/50">
                                <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <input id="attachments" name="attachments[]" type="file" multiple class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer" />
                                <p class="text-xs text-gray-400 mt-2">Accepted formats: PDF, Word, Excel, PowerPoint, Images (JPG, PNG, WebP), Videos (MP4, MOV, AVI, WebM), Audio (MP3, WAV, OGG), ZIP (Max: 50MB per file)</p>
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
                            rows: [],
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
                                    <p class="text-xs text-gray-500">Add other registered KSG colleagues who co-developed this innovation.</p>
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
                                No collaborators added yet. Click "+ Add Collaborator" to invite colleagues.
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="pt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                            <a href="{{ route('innovations.index') }}" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:text-gray-800 transition">
                                Cancel
                            </a>
                            <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition">
                                Save Draft Innovation
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
