<?= $this->extend('layout/templateAuth'); ?>
<?= $this->section('content') ?>

<h2><?= esc($title) ?> ke CargoWing</h2>
<?php if (session()->getFlashdata('error')): ?>
    <div id="errorAlert" class="error-message">
        <?= session()->getFlashdata('error'); ?>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('success')): ?>
    <div id="successAlert" class="success-message">
        <?= session()->getFlashdata('success'); ?>
    </div>
<?php endif; ?>

<form id="loginForm" action="<?= base_url('loginProcess') ?>" method="post">
    <input id="email" class="form-input" name="email" type="email" placeholder="Masukkan email" />
    <small id="emailError" class="error-text"></small>

    <div class="password-wrapper">
        <input id="passwordInput" class="form-input" name="password" type="password" placeholder="Masukkan password" />
        <span id="togglePassword" class="toggle-password" title="Show password">
            <!-- SVG ikon -->
        </span>
    </div>
    <small id="passwordError" class="error-text"></small>

    <div class="form-checkbox">
        <label><input type="checkbox" name="remember" /> Remember me</label>
    </div>
    <button class="btn" type="submit">Masuk</button>
</form>


<div class="footer-links">
    <a href="<?= base_url('register') ?>">Belum punya akun?</a>
</div>

<div class="copyright">
    © CargoWing – Powered by your logistics in flight
</div>
</div>

<script src="<?= base_url('assets/js/auth.js') ?>"></script>

<?= $this->endSection() ?>