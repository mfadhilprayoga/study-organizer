        </div>
    </main>
</div>
<script>
(() => {
    document.querySelectorAll('[data-toggle-password]').forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const passwordInput = toggle.closest('.auth-password-control')?.querySelector('input');
            if (!passwordInput) return;

            const showPassword = passwordInput.type === 'password';
            passwordInput.type = showPassword ? 'text' : 'password';
            toggle.setAttribute('aria-pressed', String(showPassword));
            toggle.setAttribute('aria-label', showPassword ? 'Sembunyikan password' : 'Tampilkan password');
        });
    });

    const profileTrigger = document.getElementById('sidebar-profile-trigger');
    const profilePanel = document.getElementById('sidebar-profile-panel');

    if (profileTrigger && profilePanel) {
        const closeProfilePanel = (restoreFocus = false) => {
            profilePanel.hidden = true;
            profileTrigger.setAttribute('aria-expanded', 'false');
            if (restoreFocus) profileTrigger.focus();
        };

        profileTrigger.addEventListener('click', () => {
            const isOpen = profileTrigger.getAttribute('aria-expanded') === 'true';
            profilePanel.hidden = isOpen;
            profileTrigger.setAttribute('aria-expanded', String(!isOpen));
        });

        document.addEventListener('click', (event) => {
            if (!profilePanel.hidden && !profileTrigger.contains(event.target) && !profilePanel.contains(event.target)) {
                closeProfilePanel();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !profilePanel.hidden) {
                closeProfilePanel(true);
            }
        });
    }

    const editProfileTrigger = document.getElementById('edit-profile-trigger');
    const profileDetails = document.getElementById('profile-details');
    const editProfileForm = document.getElementById('edit-profile-form');

    if (editProfileTrigger && profileDetails && editProfileForm) {
        editProfileTrigger.addEventListener('click', () => {
            profileDetails.hidden = true;
            editProfileForm.hidden = false;
            editProfileTrigger.hidden = true;
            editProfileForm.querySelector('input[name="nama"]').focus();
        });
    }
})();
</script>
</body>
</html>
