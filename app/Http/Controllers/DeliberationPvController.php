<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeliberationPvRequest;
use App\Http\Requests\UpdateDeliberationPvRequest;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\DeliberationPv;
use App\Models\Level;
use App\Models\Program;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeliberationPvController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', DeliberationPv::class);

        $user = $request->user();

        $query = DeliberationPv::query()
            ->visibleTo($user)
            ->with(['academicYear', 'program', 'level', 'semester', 'creator'])
            ->when($request->filled('class_name'), function ($q) use ($request) {
                $q->where('class_name', 'like', '%' . $request->string('class_name')->trim() . '%');
            })
            ->when($request->filled('academic_year_id'), fn ($q) => $q->where('academic_year_id', $request->integer('academic_year_id')))
            ->when($request->filled('program_id'), fn ($q) => $q->where('program_id', $request->integer('program_id')))
            ->when($request->filled('level_id'), fn ($q) => $q->where('level_id', $request->integer('level_id')))
            ->when($request->filled('semester_id'), fn ($q) => $q->where('semester_id', $request->integer('semester_id')))
            ->when($request->filled('specialty'), fn ($q) => $q->where('specialty', $request->string('specialty')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest();

        $pvs = $query->paginate(15)->withQueryString();

        return view('pvs.index', [
            'pvs' => $pvs,
            'filters' => $request->only([
                'class_name', 'academic_year_id', 'program_id',
                'level_id', 'semester_id', 'specialty', 'status',
            ]),
            'programs' => Program::orderBy('name')->get(),
            'academicYears' => AcademicYear::orderByDesc('id')->get(),
            'levels' => Level::orderBy('id')->get(),
            'semesters' => Semester::orderBy('id')->get(),
            'specialties' => DeliberationPv::SPECIALTIES_IG,
            'statuses' => DeliberationPv::STATUSES,
            'levelSemesters' => DeliberationPv::LEVEL_SEMESTERS,
            'canCreate' => $user->can('create', DeliberationPv::class),
        ]);
    }

    public function create()
    {
        $this->authorize('create', DeliberationPv::class);

        return view('pvs.create', [
            'programs' => Program::orderBy('name')->get(),
            'academicYears' => AcademicYear::orderByDesc('id')->get(),
            'levels' => Level::orderBy('id')->get(),
            'semesters' => Semester::orderBy('id')->get(),
            'specialties' => DeliberationPv::SPECIALTIES_IG,
            'levelSemesters' => DeliberationPv::LEVEL_SEMESTERS,
        ]);
    }

    public function store(StoreDeliberationPvRequest $request)
    {
        $this->authorize('create', DeliberationPv::class);

        $user = $request->user();
        $data = $request->validated();

        $programId = $user->isDa() ? $data['program_id'] : $user->program_id;
        $program = Program::findOrFail($programId);
        $level = Level::findOrFail($data['level_id']);

        $specialty = DeliberationPv::specialtyApplies($program->code, $level->code)
            ? ($data['specialty'] ?? null)
            : null;

        $createdPvs = DB::transaction(function () use ($data, $programId, $specialty, $user) {
            $items = [];

            foreach ($data['items'] as $item) {
                /** @var \Illuminate\Http\UploadedFile $uploadedFile */
                $uploadedFile = $item['file'];
                $storedPath = $uploadedFile->store('private/pvs', 'local');

                $pv = DeliberationPv::create([
                    'academic_year_id' => $data['academic_year_id'],
                    'program_id' => $programId,
                    'level_id' => $data['level_id'],
                    'semester_id' => $data['semester_id'],
                    'class_name' => trim($item['class_name']),
                    'specialty' => $specialty,
                    'deliberation_date' => $data['deliberation_date'],
                    'file_path' => $storedPath,
                    'file_name' => $uploadedFile->getClientOriginalName(),
                    'file_type' => $uploadedFile->getMimeType() ?? $uploadedFile->getClientMimeType(),
                    'file_size' => $uploadedFile->getSize(),
                    'status' => DeliberationPv::STATUS_SOUMIS,
                    'created_by' => $user->id,
                ]);

                $this->logAction($user->id, AuditLog::ACTION_CREATED, $pv, "PV {$pv->class_name} soumis");
                $items[] = $pv;
            }

            return $items;
        });

        $count = count($createdPvs);

        return redirect()
            ->route('pvs.index')
            ->with('success', $count > 1 ? "{$count} PV soumis." : 'PV soumis.');
    }

    public function show(Request $request, DeliberationPv $pv)
    {
        $this->authorize('view', $pv);

        $this->logAction($request->user()->id, AuditLog::ACTION_VIEWED, $pv);

        $pv->load(['academicYear', 'program', 'level', 'semester', 'creator']);

        return view('pvs.show', [
            'pv' => $pv,
            'canUpdate' => $request->user()->can('update', $pv),
            'canArchive' => $request->user()->can('archive', $pv),
            'canDelete' => $request->user()->can('delete', $pv),
        ]);
    }

    public function edit(DeliberationPv $pv)
    {
        $this->authorize('update', $pv);

        return view('pvs.edit', [
            'pv' => $pv,
            'programs' => Program::orderBy('name')->get(),
            'academicYears' => AcademicYear::orderByDesc('id')->get(),
            'levels' => Level::orderBy('id')->get(),
            'semesters' => Semester::orderBy('id')->get(),
            'specialties' => DeliberationPv::SPECIALTIES_IG,
            'levelSemesters' => DeliberationPv::LEVEL_SEMESTERS,
        ]);
    }

    public function update(UpdateDeliberationPvRequest $request, DeliberationPv $pv)
    {
        $this->authorize('update', $pv);

        $user = $request->user();
        $data = $request->validated();

        $programId = $user->isDa() ? $data['program_id'] : $pv->program_id;
        $program = Program::findOrFail($programId);
        $level = Level::findOrFail($data['level_id']);

        $specialty = DeliberationPv::specialtyApplies($program->code, $level->code)
            ? ($data['specialty'] ?? null)
            : null;

        $payload = [
            'academic_year_id' => $data['academic_year_id'],
            'program_id' => $programId,
            'level_id' => $data['level_id'],
            'semester_id' => $data['semester_id'],
            'class_name' => trim($data['class_name']),
            'specialty' => $specialty,
            'deliberation_date' => $data['deliberation_date'],
        ];

        if ($request->hasFile('file')) {
            if ($pv->file_path && Storage::disk('local')->exists($pv->file_path)) {
                Storage::disk('local')->delete($pv->file_path);
            }

            $uploadedFile = $request->file('file');
            $payload['file_path'] = $uploadedFile->store('private/pvs', 'local');
            $payload['file_name'] = $uploadedFile->getClientOriginalName();
            $payload['file_type'] = $uploadedFile->getMimeType() ?? $uploadedFile->getClientMimeType();
            $payload['file_size'] = $uploadedFile->getSize();
        }

        $pv->update($payload);

        $this->logAction($user->id, AuditLog::ACTION_UPDATED, $pv, "PV {$pv->class_name} mis à jour");

        return redirect()
            ->route('pvs.show', $pv)
            ->with('success', 'PV mis à jour.');
    }

    public function archive(Request $request, DeliberationPv $pv)
    {
        $this->authorize('archive', $pv);

        $pv->update([
            'status' => DeliberationPv::STATUS_ARCHIVE,
            'archived_at' => now(),
        ]);

        $this->logAction($request->user()->id, AuditLog::ACTION_ARCHIVED, $pv, "PV {$pv->class_name} archivé");

        return back()->with('success', 'PV archivé.');
    }

    public function destroy(Request $request, DeliberationPv $pv)
    {
        $this->authorize('delete', $pv);

        $this->logAction($request->user()->id, AuditLog::ACTION_DELETED, $pv, "PV {$pv->class_name} supprimé");

        $pv->delete();

        return redirect()
            ->route('pvs.index')
            ->with('success', 'PV supprimé.');
    }

    public function viewFile(Request $request, DeliberationPv $pv)
    {
        $this->authorize('viewFile', $pv);

        abort_unless($pv->file_path && Storage::disk('local')->exists($pv->file_path), 404);

        $this->logAction($request->user()->id, AuditLog::ACTION_VIEWED, $pv, "Consultation du fichier {$pv->file_name}");

        return Storage::disk('local')->response(
            $pv->file_path,
            $pv->file_name,
            ['Content-Type' => $pv->file_type ?: 'application/octet-stream']
        );
    }

    private function logAction(int $userId, string $action, DeliberationPv $pv, ?string $details = null): void
    {
        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'deliberation_pv_id' => $pv->id,
            'details' => $details,
        ]);
    }
}