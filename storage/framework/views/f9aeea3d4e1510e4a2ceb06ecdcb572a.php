

<?php $__env->startSection('title', 'Bosh sahifa'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container position-relative">
            <img src="<?php echo e(asset('images/logo.png')); ?>" alt="ISC - International Scientific Conferences" class="hero-logo">
            <h1 class="hero-title">International Scientific Conferences</h1>
            <p class="hero-subtitle">
                The international scientific conferences platform provides researchers and education professionals with the opportunity to publish their original scientific articles. It is an open-access, peer-reviewed monthly conference.
            </p>
            <p class="text-muted mb-4">
                <i class="bi bi-calendar3 me-2"></i>Publication Frequency: Monthly
            </p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="#conferences" class="btn btn-primary btn-lg px-4">
                    <i class="bi bi-journal-text me-2"></i>Conferences
                </a>
                <a href="<?php echo e(route('countries')); ?>" class="btn btn-outline-primary btn-lg px-4">
                    <i class="bi bi-globe me-2"></i>All Countries
                </a>
            </div>
        </div>
        <div class="hero-wave"></div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo e($countries->count()); ?>+</div>
                        <div class="stat-label"><i class="bi bi-globe me-1"></i>Countries</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo e($totalArticles ?? '500'); ?>+</div>
                        <div class="stat-label"><i class="bi bi-file-text me-1"></i>Articles</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo e($countries->count()); ?>+</div>
                        <div class="stat-label"><i class="bi bi-calendar-event me-1"></i>Conferences</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Conferences Section - 2 per row -->
    <section class="py-5" id="conferences">
        <div class="container">
            <h2 class="section-title text-center">
                <i class="bi bi-journals me-2"></i>Conferences
            </h2>
            <p class="text-center text-muted mb-5">
                Scientific conference proceedings on multidisciplinary research are electronic conference series.
            </p>

            <div class="row g-4">
                <?php $__empty_1 = true; $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="col-md-6">
                        <a href="<?php echo e(route('country.show', $country)); ?>" class="text-decoration-none">
                            <div class="card conference-card h-100 border-0 shadow-sm overflow-hidden">
                                <!-- Cover Image Header -->
                                <div class="card-img-top position-relative" style="height: 280px; overflow: hidden; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                    <?php if($country->cover_image): ?>
                                        <img src="<?php echo e(asset($country->cover_image)); ?>" 
                                             alt="<?php echo e($country->name); ?>"
                                             style="width: 100%; height: 100%; object-fit: contain; padding: 10px;">
                                    <?php else: ?>
                                        <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #1a3a5f 0%, #0d2137 100%); display: flex; align-items: center; justify-content: center;">
                                            <?php if($country->flag_url): ?>
                                                <img src="<?php echo e(Storage::url($country->flag_url)); ?>" 
                                                     alt="<?php echo e($country->name); ?>"
                                                     style="width: 100px; height: 65px; object-fit: cover; border-radius: 5px; box-shadow: 0 5px 20px rgba(0,0,0,0.3);">
                                            <?php else: ?>
                                                <i class="bi bi-globe-americas text-white" style="font-size: 4rem; opacity: 0.5;"></i>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <!-- Country badge overlay -->
                                    <div class="position-absolute top-0 end-0 m-3">
                                        <span class="badge bg-white text-dark shadow-sm px-3 py-2">
                                            <?php if($country->flag_url): ?>
                                                <img src="<?php echo e(Storage::url($country->flag_url)); ?>" 
                                                     style="width: 20px; height: 14px; object-fit: cover; border-radius: 2px; margin-right: 5px;">
                                            <?php endif; ?>
                                            <?php echo e($country->name); ?>

                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Card Body -->
                                <div class="card-body p-4">
                                    <h5 class="card-title mb-2" style="color: var(--primary-blue); font-weight: 700;">
                                        <?php echo e($country->conference_name ?? 'Conference title goes here'); ?>

                                    </h5>
                                    <p class="text-muted small mb-3">
                                        <?php echo e($country->name); ?> (<?php echo e($country->name_en); ?>)
                                    </p>
                                    <?php if($country->conference_description): ?>
                                        <p class="card-text small text-muted mb-3">
                                            <?php echo e(Str::limit($country->conference_description, 120)); ?>

                                        </p>
                                    <?php else: ?>
                                        <p class="card-text small text-muted mb-3">
                                            Scientific conference proceedings on multidisciplinary research. These conference proceedings publish original research papers presented by conference participants.
                                        </p>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-primary me-2">
                                                <i class="bi bi-file-earmark-text me-1"></i><?php echo e($country->articles_count ?? 0); ?> articles
                                            </span>
                                        </div>
                                        <span class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-arrow-right me-1"></i>View Articles
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-globe display-1 text-muted"></i>
                        <p class="text-muted mt-3">No conferences available at the moment.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h2 class="section-title">About Us</h2>
                    <p class="text-muted mb-4">
                        International Scientific Conferences (ISC) provides researchers and education professionals the opportunity to publish their original scientific articles in educational practices and related fields.
                    </p>
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <strong>Open Access</strong> - All articles are available to read for free
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <strong>Peer Review</strong> - All articles are peer-reviewed by experts
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <strong>Monthly Publication</strong> - New conferences every month
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <strong>Certificate</strong> - Providing certificates for authors
                        </li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-4">
                                <i class="bi bi-info-circle me-2 text-primary"></i>How it works?
                            </h5>
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-primary rounded-circle p-2">1</span>
                                </div>
                                <div class="ms-3">
                                    <strong>Contact Administrator</strong>
                                    <p class="text-muted small mb-0">Reach out to our platform administrators</p>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-primary rounded-circle p-2">2</span>
                                </div>
                                <div class="ms-3">
                                    <strong>Upload Article</strong>
                                    <p class="text-muted small mb-0">Send your scientific article</p>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-primary rounded-circle p-2">3</span>
                                </div>
                                <div class="ms-3">
                                    <strong>Review Process</strong>
                                    <p class="text-muted small mb-0">Our experts review the article</p>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-success rounded-circle p-2">4</span>
                                </div>
                                <div class="ms-3">
                                    <strong>Publication & Certificate</strong>
                                    <p class="text-muted small mb-0">Article is published and a certificate is issued</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <style>
        .conference-card {
            transition: all 0.3s ease;
            border-radius: 15px;
        }
        .conference-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
        }
        .conference-card .card-img-top img {
            transition: transform 0.5s ease;
        }
        .conference-card:hover .card-img-top img {
            transform: scale(1.05);
        }
    </style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\artiqle\resources\views/public/home.blade.php ENDPATH**/ ?>