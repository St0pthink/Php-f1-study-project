<div class="modal-comments">
    <h5 class="mb-3"><i class="fas fa-comments"></i> Комментарии ({{ $driver->comments->count() }})</h5>

    @auth
        <form action="{{ route('comments.store', $driver) }}" method="POST" class="comment-form mb-3">
            @csrf
            <div class="form-group">
                <textarea name="body" placeholder="Напишите комментарий..." required class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-sm mt-2">
                <i class="fas fa-paper-plane"></i> Отправить
            </button>
        </form>
    @else
        <p class="text-muted"><a href="{{ route('login') }}">Войдите</a>, чтобы оставить комментарий.</p>
    @endauth

    <div class="comments-list">
        @forelse($driver->comments->sortByDesc('created_at') as $comment)
            @php
                $isFriend = auth()->check() && in_array($comment->user_id, $friendIds ?? []);
            @endphp
            <div class="comment-item {{ $isFriend ? 'comment-friend' : '' }}">
                <div class="comment-header">
                    <div>
                        <span class="comment-author">{{ $comment->user->name }}</span>
                        @if($isFriend)
                            <span class="friend-badge"><i class="fas fa-user-friends"></i> Друг</span>
                        @endif
                    </div>
                    <span class="comment-date">{{ $comment->created_at->format('d.m.Y H:i') }}</span>
                </div>
                <div class="comment-body">
                    {{ $comment->body }}
                </div>
                @auth
                    @if(auth()->user()->is_admin ?? false)
                        <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="mt-2" 
                              onsubmit="return confirm('Удалить комментарий?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete-comment">
                                <i class="fas fa-trash"></i> Удалить
                            </button>
                        </form>
                    @endif
                @endauth
            </div>
        @empty
            <p class="text-muted">Комментариев пока нет</p>
        @endforelse
    </div>
</div>
