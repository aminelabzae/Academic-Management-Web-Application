<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordChangeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FiliereController;
use App\Http\Controllers\Admin\GroupeController;
use App\Http\Controllers\Admin\ProfesseurController;
use App\Http\Controllers\Admin\EtudiantController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\SyllabusController;
use App\Http\Controllers\Admin\SalleController;
use App\Http\Controllers\Admin\EmploiDuTempsController;
use App\Http\Controllers\Professeur\EmploiController as ProfEmploiController;
use App\Http\Controllers\Professeur\SyllabusController as ProfSyllabusController;
use App\Http\Controllers\Etudiant\EmploiController as EtudEmploiController;
use App\Http\Controllers\Etudiant\DashboardController as EtudDashboardController;

use App\Http\Controllers\Admin\ExamenController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\AnnonceController;
use App\Http\Controllers\Etudiant\ExamenController as EtudExamenController;

// Page d'accueil - Redirection vers login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentification
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Routes partagées Authentifiées
Route::middleware(['auth'])->group(function () {
    Route::get('/password/change', [PasswordChangeController::class, 'showChangeForm'])->name('password.change.notice');
    Route::post('/password/change', [PasswordChangeController::class, 'changePassword'])->name('password.change.update');
    
    // Notifications partagées
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});

// Routes Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('filieres/import', [FiliereController::class, 'import'])->name('filieres.import');
    Route::resource('filieres', FiliereController::class);
    
    Route::post('groupes/import', [GroupeController::class, 'import'])->name('groupes.import');
    Route::resource('groupes', GroupeController::class);

    Route::post('professeurs/import', [ProfesseurController::class, 'import'])->name('professeurs.import');
    Route::resource('professeurs', ProfesseurController::class);
    Route::get('professeurs-paie', [ProfesseurController::class, 'paie'])->name('professeurs.paie');
    
    Route::post('etudiants/import', [EtudiantController::class, 'import'])->name('etudiants.import');
    Route::resource('etudiants', EtudiantController::class);

    Route::post('modules/import', [ModuleController::class, 'import'])->name('modules.import');
    Route::resource('modules', ModuleController::class);
    Route::get('modules/{module}/syllabus', [SyllabusController::class, 'index'])->name('modules.syllabus.index');
    Route::post('modules/{module}/syllabus', [SyllabusController::class, 'store'])->name('modules.syllabus.store');
    Route::put('syllabus/{item}', [SyllabusController::class, 'update'])->name('modules.syllabus.update');
    Route::delete('syllabus/{item}', [SyllabusController::class, 'destroy'])->name('modules.syllabus.destroy');
    
    Route::post('salles/{salle}/toggle', [SalleController::class, 'toggle'])->name('salles.toggle');
    Route::resource('salles', SalleController::class);
    Route::resource('emplois', EmploiDuTempsController::class);
    Route::resource('examens', ExamenController::class);
    Route::post('absences/{attendance}/justify', [AttendanceController::class, 'justify'])->name('absences.justify');
    Route::resource('absences', AttendanceController::class);
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::resource('annonces', AnnonceController::class)->only(['index', 'create', 'store', 'destroy']);

    Route::get('archives', [\App\Http\Controllers\Admin\ArchiveController::class, 'index'])->name('archives.index');
    Route::get('archives/{year}/{month}', [\App\Http\Controllers\Admin\ArchiveController::class, 'show'])->name('archives.show');

    Route::get('emplois-grille', [EmploiDuTempsController::class, 'grille'])->name('emplois.grille');
    Route::get('emplois-grille-semaine', [EmploiDuTempsController::class, 'grilleSemaine'])->name('emplois.grille-semaine');
    Route::post('emplois/generate', [EmploiDuTempsController::class, 'generateWeeklySessions'])->name('emplois.generate');
    Route::post('emplois/{emploi}/approve', [EmploiDuTempsController::class, 'approveAnnulation'])->name('emplois.approve');
    Route::post('emplois/{emploi}/reject', [EmploiDuTempsController::class, 'rejectAnnulation'])->name('emplois.reject');
    Route::get('emplois-pdf', [EmploiDuTempsController::class, 'exportPdf'])->name('emplois.pdf');
    Route::get('emplois-global-pdf', [EmploiDuTempsController::class, 'exportGlobalPdf'])->name('emplois.global-pdf');
    Route::get('emplois-filter-data', [EmploiDuTempsController::class, 'getFilteredData'])->name('emplois.filter-data');
});

// Routes Professeur
Route::middleware(['auth', 'professeur'])->prefix('professeur')->name('professeur.')->group(function () {
    Route::get('/dashboard', [ProfEmploiController::class, 'dashboard'])->name('dashboard');
    Route::get('/emploi', [ProfEmploiController::class, 'index'])->name('emploi');
    Route::post('/emploi/{emploi}/cancel', [ProfEmploiController::class, 'demandeAnnulation'])->name('emploi.cancel');
    Route::post('/emploi/{emploi}/toggle-examen', [ProfEmploiController::class, 'toggleExamen'])->name('emploi.toggle-examen');
    Route::post('/emploi/{emploi}/update-link', [ProfEmploiController::class, 'updateTeamsLink'])->name('emploi.update-link');
    Route::post('/emploi/{emploi}/confirmer', [ProfEmploiController::class, 'confirmerSeance'])->name('emploi.confirmer');
    Route::get('/seances-realisees', [ProfEmploiController::class, 'seancesRealisees'])->name('seances.realisees');
    Route::get('/absences', [ProfEmploiController::class, 'absences'])->name('absences');
    Route::get('/avancement', [ProfEmploiController::class, 'avancement'])->name('avancement');
    Route::post('/modules/{module}/syllabus', [ProfSyllabusController::class, 'store'])->name('syllabus.store');
});

// Routes Étudiant
Route::middleware(['auth', 'etudiant'])->prefix('etudiant')->name('etudiant.')->group(function () {
    Route::get('/dashboard', [EtudDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/emploi', [EtudEmploiController::class, 'index'])->name('emploi');
    Route::get('/examens', [EtudExamenController::class, 'index'])->name('examens.index');
    Route::post('/annonces/{annonce}/read', [EtudDashboardController::class, 'markAnnonceAsRead'])->name('annonces.read');
});