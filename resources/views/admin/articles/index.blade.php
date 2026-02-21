@extends('layouts.admin')

@section('page-title', 'Maqolalar')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-file-text me-2"></i>Barcha maqolalar</span>
            <a href="{{ route('admin.articles.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus me-1"></i>Yangi maqola
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Sarlavha</th>
                        <th>Muallif</th>
                        <th>Konferensiya</th>
                        <th>Betlar</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $article)
                        <tr>
                            <td><strong>{{ Str::limit($article->title, 35) }}</strong></td>
                            <td>{{ Str::limit($article->author_display_name, 25) }}</td>
                            <td class="text-muted small">
                                {{ $article->conference->country->name }} -
                                {{ Str::limit($article->conference->country->conference_name ?? $article->conference->title, 30) }}
                            </td>
                            <td>{{ $article->page_range }}</td>
                            <td>
                                @if($article->status === 'published')
                                    <span class="badge bg-success">Nashr etilgan</span>
                                @else
                                    <span class="badge bg-warning">Kutilmoqda</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.articles.show', $article) }}" class="btn btn-sm btn-outline-info"><i
                                        class="bi bi-eye"></i></a>
                                <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-sm btn-outline-primary"><i
                                        class="bi bi-pencil"></i></a>
                                @if($article->status === 'pending')
                                    <form action="{{ route('admin.articles.publish', $article) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-success" title="Nashr qilish"><i
                                                class="bi bi-check"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Maqolalar mavjud emas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($articles->hasPages())
            <div class="card-footer">{{ $articles->links() }}</div>
        @endif
    </div>
@endsection