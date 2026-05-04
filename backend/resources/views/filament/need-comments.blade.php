<div class="space-y-3 max-h-96 overflow-y-auto p-1">
    @forelse($need->comments()->with('user')->latest()->get() as $comment)
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-1">
                <span class="font-semibold text-sm text-gray-800 dark:text-gray-200">
                    {{ $comment->user?->name ?? 'مجهول' }}
                    <span class="text-xs font-normal text-gray-500 ms-1">
                        ({{ match($comment->user?->role) { 'admin' => 'أدمن', 'volunteer' => 'متطوع', 'organization' => 'منظمة', default => '' } }})
                    </span>
                </span>
                <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
            </div>
            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $comment->body }}</p>
        </div>
    @empty
        <p class="text-gray-500 text-sm text-center py-6">لا توجد تعليقات على هذا الطلب بعد.</p>
    @endforelse
</div>
