    </div><!-- /#content -->
    <footer class="sticky-footer bg-white">
      <div class="container my-auto">
        <div class="copyright text-center my-auto">
          <span>©  La Paz - Bolivia <?php echo date('Y'); ?></span>
        </div>
      </div>
    </footer>
  </div><!-- /#content-wrapper -->
</div><!-- /#wrapper -->


<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/js/sb-admin-2.min.js"></script>

<script>
(function() {
  const KEY = 'gbt-theme'; // clave localStorage
  const root = document.documentElement;

  // 1) Determinar tema inicial
  function getPreferredTheme() {
    const saved = localStorage.getItem(KEY);
    if (saved === 'light' || saved === 'dark') return saved;
    // si no hay preferencia guardada, usa sistema
    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
      ? 'dark' : 'light';
  }

  function applyTheme(theme) {
    root.setAttribute('data-theme', theme);
    const btn = document.getElementById('themeToggle');
    if (btn) {
      const icon = btn.querySelector('i');
      if (icon) icon.className = (theme === 'dark') ? 'fas fa-sun' : 'fas fa-moon';
      btn.classList.toggle('btn-dark', theme === 'dark');
      btn.classList.toggle('btn-light', theme !== 'dark');
    }
  }

  // 2) Aplicar al cargar
  let current = getPreferredTheme();
  applyTheme(current);

  // 3) Escuchar cambios del SO si no hay preferencia explícita
  const media = window.matchMedia('(prefers-color-scheme: dark)');
  media.addEventListener && media.addEventListener('change', (e) => {
    const saved = localStorage.getItem(KEY);
    if (saved !== 'light' && saved !== 'dark') {
      current = e.matches ? 'dark' : 'light';
      applyTheme(current);
    }
  });

  // 4) Toggle manual
  document.addEventListener('click', function(e) {
    const t = e.target.closest('#themeToggle');
    if (!t) return;
    current = (root.getAttribute('data-theme') === 'dark') ? 'light' : 'dark';
    localStorage.setItem(KEY, current);
    applyTheme(current);
  });
})();
</script>



</body>
</html>
