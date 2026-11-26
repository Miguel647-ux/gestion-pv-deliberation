<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Journal — ENEAM PV Archive</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 2rem; }
        h1 { margin-bottom: 1rem; }
        form.filters { margin-bottom: 1rem; display: flex; gap: .5rem; align-items: center; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: .5rem .75rem; text-align: left; vertical-align: top; }
        th { background: #f3f4f6; }
        .action { font-family: monospace; padding: .15rem .5rem; border-radius: 4px; font-size: .85rem; }
        .action-created  { background: #dcfce7; color: #166534; }
        .action-updated  { background: #fef3c7; color: #92400e; }
        .action-archived { background: #dbeafe; color: #1e40af; }
        .action-viewed   { background: #f3f4f6; color: #374151; }
        .action-deleted  { background: #fee2e2; color: #991b1b; }
        .pagination { margin-top: 1rem; }
    </style>
</head>
<body>
    <h1>Journal des actions ({{ $logs->total() }})</h1>
    <p><a href="{{ route('dashboard') }}">← Retour au tableau de bord</a></p>

    <form class="filters" method="GET" action="{{ route('admin.logs.index') }}">
        <label for="action">Action :</label>
        <select name="action" id="action">
            <option value="">— Toutes —</option>
            @foreach ($actions as $action)
    <option value="{{ $action }}" @selected($currentAction === $action)>
        {{ App\Models\AuditLog::labels()[$action] ?? $action }}
    </option>
@endforeach
        </select>
        <button type="submit">Filtrer</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Utilisateur</th>
                <th>Action</th>
                <th>PV concerné</th>
                <th>Détails</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $log->user?->name ?? '—' }}</td>
                    <td>
    @php
        $labels = [
            'created'  => 'Création',
            'updated'  => 'Modification',
            'archived' => 'Archivage',
            'viewed'   => 'Consultation',
            'deleted'  => 'Suppression',
        ];
    @endphp
    <td>
    <span class="action action-{{ $log->action }}">{{ $log->label() }}</span>
</td>
</td>
                    <td>
                        @if ($log->deliberationPv)
                            {{ $log->deliberationPv->class_name }}
                            <small>({{ $log->deliberationPv->program?->code }})</small>
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $log->details ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Aucune action journalisée.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination">
        {{ $logs->links() }}
    </div>
</body>
</html>