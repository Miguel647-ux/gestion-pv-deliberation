<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Utilisateurs — ENEAM PV Archive</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 2rem; }
        h1 { margin-bottom: 1rem; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: .5rem .75rem; text-align: left; }
        th { background: #f3f4f6; }
        .badge { padding: .15rem .5rem; border-radius: 4px; font-size: .85rem; }
        .badge-da { background: #dbeafe; color: #1e40af; }
        .badge-cd { background: #dcfce7; color: #166534; }
    </style>
</head>
<body>
    <h1>Utilisateurs ({{ $users->count() }})</h1>
    <p><a href="{{ route('dashboard') }}">← Retour au tableau de bord</a></p>

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Filière rattachée</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if ($user->isDa())
                            <span class="badge badge-da">DA / Admin</span>
                        @else
                            <span class="badge badge-cd">CD</span>
                        @endif
                    </td>
                    <td>{{ $user->program?->name ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="4">Aucun utilisateur.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>