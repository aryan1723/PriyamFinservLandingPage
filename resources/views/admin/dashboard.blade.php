@extends('admin.layout')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="font-size: 1.8rem; color: var(--primary);">Dashboard Overview</h1>
        <a href="{{ route('admin.queries') }}" class="btn"><i class="fa-solid fa-plus"></i> View Appointments</a>
    </div> 

    <!-- Upcoming Schedule & Announcement Row -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2.5rem; margin-bottom: 2.5rem;">
        
        <!-- Upcoming Schedule -->
        <div class="card" style="margin-bottom: 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="font-size: 1.3rem; margin-bottom: 0;"><i class="fa-solid fa-calendar-days" style="margin-right: 0.5rem; color: var(--primary);"></i> Upcoming Schedule</h2>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem;">
                @forelse($upcoming_meetings as $meeting)
                    <div style="border: 1px solid var(--border); border-radius: 8px; overflow: hidden; background: #fff; transition: var(--transition); box-shadow: var(--shadow-sm);">
                        <div style="background: var(--primary-black); color: #fff; padding: 0.5rem; text-align: center; font-family: var(--font-serif); font-size: 0.9rem; letter-spacing: 1px;">
                            {{ \Carbon\Carbon::parse($meeting->appointment_date)->format('F Y') }}
                        </div>
                        <div style="padding: 1.5rem 1rem; text-align: center;">
                            <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary); line-height: 1;">
                                {{ \Carbon\Carbon::parse($meeting->appointment_date)->format('d') }}
                            </div>
                            <div style="font-size: 0.85rem; color: #888; text-transform: uppercase; margin-bottom: 1rem;">
                                {{ \Carbon\Carbon::parse($meeting->appointment_date)->format('l') }} &bull; {{ \Carbon\Carbon::parse($meeting->appointment_date)->format('g:i A') }}
                            </div>
                            <div style="font-weight: 600; color: #222; font-size: 1.05rem; margin-bottom: 1rem;">
                                {{ Str::limit($meeting->user->name ?? $meeting->company_name ?? 'Client Meeting', 20) }}
                            </div>
                            <a href="{{ $meeting->user_id ? route('admin.users.show', $meeting->user_id) : '#' }}" class="btn btn-outline" style="width: 100%; padding: 0.4rem; font-size: 0.8rem;">View Details</a>
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; border: 1px dashed var(--border); border-radius: 8px; color: #888;">
                        <i class="fa-regular fa-calendar-xmark" style="font-size: 2.5rem; margin-bottom: 1rem; color: #ddd; display: block;"></i>
                        Your calendar is clear. No upcoming meetings scheduled.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Announcement Manager -->
        <div class="card" style="margin-bottom: 0; padding: 0; overflow: hidden;">
            <div style="background: var(--primary-black); color: #fff; padding: 1.2rem 1.5rem; display: flex; align-items: center; gap: 0.6rem;">
                <i class="fa-solid fa-bullhorn"></i>
                <h2 style="font-size: 1.1rem; margin: 0; color: #fff;">Announcements</h2>
            </div>

            <!-- Announcements List -->
            <div style="max-height: 260px; overflow-y: auto;">
                @forelse($announcements as $ann)
                    <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.85rem 1.2rem; border-bottom: 1px solid var(--border); {{ $ann->is_active ? 'background: #f0fdf4;' : '' }}">
                        <div style="flex: 1; min-width: 0;">
                            <p style="font-size: 0.85rem; color: #222; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $ann->message }}">
                                {{ $ann->message }}
                            </p>
                            <span style="font-size: 0.72rem; color: #999;">{{ $ann->created_at->format('M d, Y') }}</span>
                        </div>
                        @if($ann->is_active)
                            <span style="background: #dcfce7; color: #166534; font-size: 0.7rem; padding: 0.15rem 0.5rem; border-radius: 10px; font-weight: 700; white-space: nowrap; flex-shrink: 0;">LIVE</span>
                        @endif
                        <!-- Toggle Active -->
                        <form action="{{ route('admin.announcement.toggle', $ann->id) }}" method="POST">
                            @csrf
                            <button type="submit" title="{{ $ann->is_active ? 'Deactivate' : 'Set Active' }}"
                                style="background: none; border: 1px solid {{ $ann->is_active ? '#16a34a' : '#d1d5db' }}; border-radius: 6px; padding: 0.25rem 0.5rem; cursor: pointer; color: {{ $ann->is_active ? '#16a34a' : '#9ca3af' }}; font-size: 0.75rem;">
                                <i class="fa-solid {{ $ann->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                            </button>
                        </form>
                        <!-- Delete -->
                        <button type="button" title="Delete" onclick="confirmAnnDelete('{{ route('admin.announcement.delete', $ann->id) }}')" style="background: none; border: 1px solid #fca5a5; border-radius: 6px; padding: 0.25rem 0.5rem; cursor: pointer; color: #dc3545; font-size: 0.75rem;">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                @empty
                    <div style="padding: 1.5rem; text-align: center; color: #aaa; font-size: 0.85rem;">
                        <i class="fa-regular fa-bell-slash" style="display: block; font-size: 1.5rem; margin-bottom: 0.4rem;"></i>
                        No announcements yet.
                    </div>
                @endforelse
            </div>

            <!-- New Announcement Form -->
            <div style="padding: 1rem 1.2rem; border-top: 1px solid var(--border); background: #fafafa;">
                <form action="{{ route('admin.announcement') }}" method="POST" style="display: flex; gap: 0.6rem;">
                    @csrf
                    <input type="text" name="message" class="form-control" placeholder="Type new announcement..." required style="flex: 1; font-size: 0.85rem; padding: 0.5rem 0.8rem;">
                    <button type="submit" class="btn" style="white-space: nowrap; font-size: 0.78rem; padding: 0.5rem 1rem;">
                        <i class="fa-solid fa-plus"></i> Post
                    </button>
                </form>
            </div>
        </div>

    </div>
    
    <!-- Stats Row -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
        <div class="card" style="border-left: 4px solid var(--primary); padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h3 style="color: #666; font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Total Clients</h3>
                    <div style="font-size: 2.2rem; font-weight: 700; margin-top: 0.5rem; color: var(--primary);">{{ $stats['users'] }}</div>
                </div>
                <div style="background: #f0f0f0; width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: #666;">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
        </div>
        <div class="card" style="border-left: 4px solid var(--primary); padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h3 style="color: #666; font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Total Queries</h3>
                    <div style="font-size: 2.2rem; font-weight: 700; margin-top: 0.5rem; color: var(--primary);">{{ $stats['queries'] }}</div>
                </div>
                <div style="background: #f0f0f0; width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: #666;">
                    <i class="fa-solid fa-clipboard-question"></i>
                </div>
            </div>
        </div>
        <div class="card" style="border-left: 4px solid #e65100; padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h3 style="color: #666; font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Pending Actions</h3>
                    <div style="font-size: 2.2rem; font-weight: 700; margin-top: 0.5rem; color: #e65100;">{{ $stats['pending_queries'] }}</div>
                </div>
                <div style="background: #fff3e0; width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: #e65100;">
                    <i class="fa-solid fa-bell"></i>
                </div>
            </div>
        </div>
        <div class="card" style="border-left: 4px solid var(--primary); padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h3 style="color: #666; font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Blog Posts</h3>
                    <div style="font-size: 2.2rem; font-weight: 700; margin-top: 0.5rem; color: var(--primary);">{{ $stats['blogs'] }}</div>
                </div>
                <div style="background: #f0f0f0; width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: #666;">
                    <i class="fa-solid fa-newspaper"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Queries -->
    <div class="card">
        <div class="card-header">
            <h2 style="font-size: 1.3rem;">Recent Queries Overview</h2>
            <a href="{{ route('admin.queries') }}" class="btn btn-outline" style="padding: 0.4rem 1rem; font-size: 0.85rem;">View All</a>
        </div>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Client / Company</th>
                        <th>Requirement</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_queries as $query)
                        <tr>
                            <td>
                                <div style="font-weight: 600; color: #222;">{{ $query->company_name ?? 'Guest' }}</div>
                            </td>
                            <td style="color: #555;">{{ Str::limit($query->requirement, 60) }}</td>
                            <td>
                                <span class="badge {{ $query->status === 'resolved' ? 'badge-resolved' : 'badge-pending' }}">
                                    <i class="fa-solid {{ $query->status === 'resolved' ? 'fa-check' : 'fa-clock' }}"></i> {{ ucfirst($query->status) }}
                                </span>
                            </td>
                            <td style="color: #666; font-size: 0.9rem;">{{ $query->created_at->format('M d, Y') }}</td>
                            <td>
                                @if($query->status === 'resolved')
                                    <form action="{{ route('admin.queries.delete', $query->id) }}" method="POST" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline" style="padding: 0.2rem 0.5rem; font-size: 0.75rem; color: #dc3545; border-color: #dc3545;"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem; color: #888;">No recent queries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Delete Confirmation Modal (queries) -->
    <div id="deleteModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 2000; align-items: center; justify-content: center;">
        <div style="background: #fff; width: 100%; max-width: 400px; border-radius: 12px; padding: 2.5rem; text-align: center; margin: 2rem;">
            <i class="fa-solid fa-triangle-exclamation" style="font-size: 3rem; color: #dc3545; margin-bottom: 1rem;"></i>
            <h3 style="margin-bottom: 0.5rem;">Delete this record?</h3>
            <p style="color: #666; margin-bottom: 2rem; font-size: 0.95rem;">This action cannot be undone.</p>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <button onclick="document.getElementById('deleteModal').style.display='none'" class="btn btn-outline">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn" style="background: #dc3545; border-color: #dc3545;"><i class="fa-solid fa-trash"></i> Delete</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Announcement Delete Modal -->
    <div id="annDeleteModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 2000; align-items: center; justify-content: center;">
        <div style="background: #fff; width: 100%; max-width: 380px; border-radius: 12px; padding: 2.5rem; text-align: center; margin: 2rem;">
            <i class="fa-solid fa-bullhorn" style="font-size: 2.5rem; color: #dc3545; margin-bottom: 1rem;"></i>
            <h3 style="margin-bottom: 0.5rem;">Delete Announcement?</h3>
            <p style="color: #666; margin-bottom: 2rem; font-size: 0.9rem;">This announcement will be permanently removed.</p>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <button onclick="document.getElementById('annDeleteModal').style.display='none'" class="btn btn-outline">Cancel</button>
                <form id="annDeleteForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn" style="background: #dc3545; border-color: #dc3545; color:#fff;"><i class="fa-solid fa-trash"></i> Delete</button>
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

        function confirmAnnDelete(url) {
            document.getElementById('annDeleteForm').action = url;
            document.getElementById('annDeleteModal').style.display = 'flex';
        }
    </script>
@endsection
