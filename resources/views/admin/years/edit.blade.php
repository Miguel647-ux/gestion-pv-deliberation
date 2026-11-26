<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier l'année — ENEAM PV Archive</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 2rem; max-width: 600px; }
        h1 { margin-bottom: 1rem; }
        form { display: flex; flex-direction: column; gap: 1rem; }
        label { display: block; font-weight: 600; margin-bottom: .25rem; }
        input[type=text], input[type=date] { width: 100%; padding: .5rem; border: 1px solid #ccc; border-radius: 4px; }
        input[type=checkbox] { margin-right: .5rem; }
        .error { color: #b91c1c; font-size: .9rem; margin-top: .25rem; }
        button { padding: .5rem 1rem; background: #1e40af; color: #fff; border: none; border-radius: 4px; cursor: pointer; width: fit-content; }
    </style>
</head>
<body>
    <h1>Modifier {{ $year->label }}</h1>
    <p><a href="{{ route('admin.years.index') }}">← Retour</a></p>

    <form method="POST" action="{{ route('admin.years.update', $year) }}">
        @csrf
        @method('PUT')

        <div>
            <label for="label">Libellé (ex. 2025-2026)</label>
            <input type="text" id="label" name="label" value="{{ old('label', $year->label) }}" required>
            @error('label') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div>
            <label for="starts_on">Date de début</label>
            <input type="date" id="starts_on" name="starts_on"
                   value="{{ old('starts_on', $year->starts_on->format('Y-m-d')) }}" required>
            @error('starts_on') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div>
            <label for="ends_on">Date de fin</label>
            <input type="date" id="ends_on" name="ends_on"
                   value="{{ old('ends_on', $year->ends_on->format('Y-m-d')) }}" required>
            @error('ends_on') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div>
            <label>
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $year->is_active))>
                Définir comme année active
            </label>
        </div>

        <button type="submit">Enregistrer</button>
    </form>
</body>
</html>