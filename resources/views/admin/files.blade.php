@extends('admin.layout')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="font-size: 1.8rem; color: var(--primary);">Secure File Sharing</h1>
    </div>

    @if(session('success'))
        <div style="background: #f0fdf4; color: #166534; padding: 0.75rem 1.2rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #bbf7d0; font-size: 0.9rem;">
            <i class="fa-solid fa-circle-check" style="margin-right: 6px;"></i> {{ session('success') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 350px 1fr; gap: 2.5rem; align-items: start;">
        
        <!-- Upload Form -->
        <div class="card" style="position: sticky; top: 20px;">
            <div style="text-align: center; margin-bottom: 2rem; color: var(--primary);">
                <i class="fa-solid fa-cloud-arrow-up" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                <h2 style="font-size: 1.3rem;">Share New File</h2>
                <p style="font-size: 0.85rem; color: #888; margin-top: 0.5rem;">Securely upload documents to specific clients.</p>
            </div>

            <form action="{{ route('admin.files.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="user_id">Select Client</label>
                    <select name="user_id" id="user_id" class="form-control" required style="cursor: pointer;">
                        <option value="">-- Choose a Client --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="file">Select File (Max 10MB)</label>
                    <div style="position: relative;">
                        <input type="file" name="file" id="file" class="form-control" required style="padding-left: 2.5rem; cursor: pointer;">
                        <i class="fa-solid fa-paperclip" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #888;"></i>
                    </div>
                </div>
                <button type="submit" class="btn" style="width: 100%; justify-content: center; margin-top: 1rem;"><i class="fa-solid fa-upload"></i> Upload & Share</button>
            </form>
        </div>

        <!-- Files List -->
        <div class="card" style="padding: 0;">
            <div class="card-header" style="padding: 1.5rem 2rem 0; margin-bottom: 1rem;">
                <h2 style="font-size: 1.3rem;">Shared Files History</h2>
            </div>
            
            <div style="overflow-x: auto;">
                <table style="margin-top: 0;">
                    <thead>
                        <tr>
                            <th style="padding: 1rem 2rem;">Document Details</th>
                            <th style="padding: 1rem 2rem;">Shared With</th>
                            <th style="padding: 1rem 2rem;">Uploaded By</th>
                            <th style="padding: 1rem 2rem;">Date</th>
                            <th style="padding: 1rem 2rem; width: 80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($files as $file)
                            <tr>
                                <td style="padding: 1.5rem 2rem;">
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <div style="background: #f0f0f0; width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: var(--primary);">
                                            <i class="fa-regular fa-file"></i>
                                        </div>
                                        <div>
                                            <strong style="color: #222; display: block;">{{ $file->file_name }}</strong>
                                            <span style="color: #888; font-size: 0.8rem; text-transform: uppercase;">{{ explode('/', $file->file_type ?? 'Unknown')[1] ?? 'File' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1.5rem 2rem;">
                                    <div style="font-weight: 500; color: #333;">{{ $file->user->name ?? 'Unknown' }}</div>
                                    <div style="font-size: 0.85rem; color: #888;">{{ $file->user->email ?? '' }}</div>
                                </td>
                                <td style="padding: 1.5rem 2rem;">
                                    @if($file->uploaded_by === 'admin')
                                        <span style="background: #e8f5e9; color: #2e7d32; font-size: 0.75rem; padding: 0.2rem 0.6rem; border-radius: 10px; font-weight: 600;">Admin</span>
                                    @else
                                        <span style="background: #e3f2fd; color: #1565c0; font-size: 0.75rem; padding: 0.2rem 0.6rem; border-radius: 10px; font-weight: 600;">Client</span>
                                    @endif
                                </td>
                                <td style="padding: 1.5rem 2rem; color: #666;">
                                    {{ $file->created_at->format('M d, Y') }}<br>
                                    <span style="font-size: 0.8rem; color: #aaa;">{{ $file->created_at->format('h:i A') }}</span>
                                </td>
                                <td style="padding: 1.5rem 2rem;">
                                    <form action="{{ route('admin.files.delete', $file->id) }}" method="POST" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline" style="padding: 0.3rem 0.7rem; font-size: 0.8rem; color: #dc3545; border-color: #dc3545;" title="Delete file">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 3rem; color: #888;">
                                    <i class="fa-solid fa-folder-open" style="font-size: 3rem; color: #ddd; margin-bottom: 1rem; display: block;"></i>
                                    No files have been shared yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 2000; align-items: center; justify-content: center;">
        <div style="background: #fff; width: 100%; max-width: 400px; border-radius: 12px; padding: 2.5rem; text-align: center; margin: 2rem;">
            <i class="fa-solid fa-triangle-exclamation" style="font-size: 3rem; color: #dc3545; margin-bottom: 1rem;"></i>
            <h3 style="margin-bottom: 0.5rem;">Delete this file?</h3>
            <p style="color: #666; margin-bottom: 2rem; font-size: 0.95rem;">This will permanently remove the file from storage and cannot be undone.</p>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <button onclick="document.getElementById('deleteModal').style.display='none'" class="btn btn-outline">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn" style="background: #dc3545; border-color: #dc3545; color: #fff;"><i class="fa-solid fa-trash"></i> Delete</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.delete-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                document.getElementById('deleteForm').action = this.action;
                document.getElementById('deleteModal').style.display = 'flex';
            });
        });
    </script>
@endsection
