<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Années académiques — ENEAM PV Archive</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 2rem; }
        h1 { margin-bottom: 1rem; }
        .flash { padding: .75rem 1rem; margin-bottom: 1rem; border-radius: 4px; }
        .flash-status { background: #dcfce7; color: #166534; }
        .flash-error  { background: #fee2e2; color: #991b1b; }
        table { border-collapse: collapse; width: 100%; margin-top: 1rem; }
        th, td { border: 1px solid #ddd; padding: .5rem .75rem; text-align: left; }
        th { background: #f3f4f6; }
        .badge { padding: .15rem .5rem; border-radius: 4px; font-size: .85rem; }
        .badge-active { background: #dbeafe; color: #1e40af; }
        .actions a, .actions button { margin-right: .5rem; }
        .btn-link { background: none; border: none; color: #b91c1c; cursor: pointer; padding: 0; font: inherit; }
    </style>
</head>
<body>
    <h1>Années académiques ({{ $years->count() }})</h1>
    <p>
        <a href="{{ route('dashboard') }}">← Dashboard</a> &nbsp;|&nbsp;
        <a href="{{ route('admin.years.create') }}">+ Nouvelle année</a>
    </p>

    @if (session('status'))
        <div class="flash flash-status">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="flash flash-error">{{ session('error') }}</div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Libellé</th>
                <th>Début</th>
                <th>Fin</th>
                <th>Statut</th>
                <th>PV rattachés</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($years as $year)
                <tr>
                    <td>{{ $year->label }}</td>
                    <td>{{ $year->starts_on->format('d/m/Y') }}</td>
                    <td>{{ $year->ends_on->format('d/m/Y') }}</td>
                    <td>
                        @if ($year->is_active)
                            <span class="badge badge-active">Active</span>
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $year->deliberation_pvs_count }}</td>
                    <td class="actions">
                        <a href="{{ route('admin.years.edit', $year) }}">Modifier</a>
                        <form action="{{ route('admin.years.destroy', $year) }}" method="POST" style="display:inline"
                              onsubmit="return confirm('Supprimer cette année ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-link">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">Aucune année académique.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>