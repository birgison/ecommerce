{{-- ======================================== Tambahkan setelah form register
======================================== --}} {{-- Divider --}}
<div class="position-relative my-4">
  <hr />
  <span
    class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted"
  >
    atau daftar dengan
  </span>
</div>

{{-- Google Sign-up Button --}}
<div class="d-grid gap-2">
  <a href="{{ route('auth.google') }}" class="btn btn-outline-danger btn-lg">
    {{-- Google SVG Icon --}}
    <svg class="me-2" width="20" height="20" viewBox="0 0 24 24">
      {{-- ... SVG paths sama seperti di login ... --}}
    </svg>
    Daftar dengan Google
  </a>
</div>

{{-- Teks login --}}
<p class="mt-4 text-center mb-0">
  Sudah punya akun?
  <a href="{{ route('login') }}" class="text-decoration-none fw-bold">
    Login
  </a>
</p>
