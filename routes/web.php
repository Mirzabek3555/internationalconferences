<?php

use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\ConferenceController as AdminConferenceController;
use App\Http\Controllers\Admin\CountryController as AdminCountryController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/countries', [PublicController::class, 'countries'])->name('countries');
Route::get('/country/{country}', [PublicController::class, 'country'])->name('country.show');
Route::get('/conference/{conference}', [PublicController::class, 'conference'])->name('conference.show');
Route::get('/article/{article}', [PublicController::class, 'article'])->name('article.show');

/*
|--------------------------------------------------------------------------
| Sitemap Routes (for Google Scholar & SEO)
|--------------------------------------------------------------------------
*/
Route::get('/sitemap.xml', [SitemapController::class, 'xml'])->name('sitemap.xml');
Route::get('/sitemap-articles.xml', [SitemapController::class, 'articles'])->name('sitemap.articles');
Route::get('/sitemap', [SitemapController::class, 'html'])->name('sitemap');


/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', fn() => view('auth.login'))->name('login');
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
    Route::get('/register', fn() => view('auth.register'))->name('register');
    Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
});

Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/my-articles', [UserController::class, 'myArticles'])->name('articles');
    Route::get('/my-certificates', [UserController::class, 'myCertificates'])->name('certificates');
    Route::get('/article/{article}/download', [UserController::class, 'downloadArticle'])->name('article.download');
    Route::get('/certificate/{article}/download', [UserController::class, 'downloadCertificate'])->name('certificate.download');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn() => redirect()->route('admin.countries.index'))->name('dashboard');

    // Countries
    Route::resource('countries', AdminCountryController::class);
    Route::post('/countries/{country}/update-conference-name', [AdminCountryController::class, 'updateConferenceName'])
        ->name('countries.update-conference-name');

    // Conferences
    Route::resource('conferences', AdminConferenceController::class);
    Route::post('/conferences/{conference}/collection', [AdminConferenceController::class, 'generateCollection'])
        ->name('conferences.collection');
    Route::post('/conferences/{conference}/full-collection', [AdminConferenceController::class, 'generateFullCollection'])
        ->name('conferences.full-collection');
    Route::get('/conferences/{conference}/download-collection', [AdminConferenceController::class, 'downloadCollection'])
        ->name('conferences.download-collection');
    Route::post('/conferences/{conference}/complete', [AdminConferenceController::class, 'complete'])
        ->name('conferences.complete');

    // Articles
    Route::resource('articles', AdminArticleController::class);
    Route::post('/articles/{article}/publish', [AdminArticleController::class, 'publish'])
        ->name('articles.publish');
    Route::get('/articles/{article}/download-formatted', [AdminArticleController::class, 'downloadFormatted'])
        ->name('articles.download-formatted');
    Route::post('/articles/{article}/reformat', [AdminArticleController::class, 'reformatPdf'])
        ->name('articles.reformat');

    // Certificates
    Route::post('/articles/{article}/certificate', [CertificateController::class, 'generate'])
        ->name('certificates.generate');
    Route::post('/articles/{article}/certificate/regenerate', [CertificateController::class, 'regenerate'])
        ->name('certificates.regenerate');
    Route::get('/articles/{article}/certificate/download', [CertificateController::class, 'download'])
        ->name('certificates.download');
});
