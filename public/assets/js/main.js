// Validación del formulario de suscripción
const newsletterForm = document.getElementById('newsletterForm');
if (newsletterForm) {
    newsletterForm.addEventListener('submit', function(event) {
        const emailInput = this.querySelector('input[name="email"]');
        const email = emailInput.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailRegex.test(email)) {
            event.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Invalid Email',
                text: 'Please enter a valid email address.',
                confirmButtonText: 'OK'
            });
        }
    });
}

// Smooth scroll para "Back to Top"
document.querySelector('.back-to-top')?.addEventListener('click', function(event) {
    event.preventDefault();
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

// Toggle del navbar (para pantallas pequeñas)
const navItems = document.querySelector('.nav__items');
const toggleNavBtn = document.querySelector('#toggle__nav-btn');

if (navItems && toggleNavBtn) {
    const toggleNav = () => {
        const isNavOpen = navItems.style.display === 'flex';
        if (isNavOpen) {
            navItems.style.display = '';
            toggleNavBtn.innerHTML = '<i class="uil uil-bars"></i>';
        } else {
            navItems.style.display = 'flex';
            toggleNavBtn.innerHTML = '<i class="uil uil-multiply"></i>';
        }
    };
    toggleNavBtn.addEventListener('click', toggleNav);
}

// Toggle del sidebar (para dashboard)
const sidebar = document.querySelector('aside');
const showSidebarBtn = document.querySelector('#show__sidebar-btn');
const hideSidebarBtn = document.querySelector('#hide__sidebar-btn');

if (sidebar && showSidebarBtn && hideSidebarBtn) {
    const showSidebar = () => {
        sidebar.style.left = '0';
        sidebar.classList.add('show');
        showSidebarBtn.style.display = 'none';
        hideSidebarBtn.style.display = 'inline-block';
    };

    const hideSidebar = () => {
        sidebar.style.left = '-100%';
        sidebar.classList.remove('show');
        showSidebarBtn.style.display = 'inline-block';
        hideSidebarBtn.style.display = 'none';
    };

    showSidebarBtn.addEventListener('click', showSidebar);
    hideSidebarBtn.addEventListener('click', hideSidebar);

    // Combinar los eventos resize en uno solo
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 600) {
            sidebar.style.left = '0';
            sidebar.classList.remove('show');
            showSidebarBtn.style.display = 'none';
            hideSidebarBtn.style.display = 'none';
        } else if (!sidebar.classList.contains('show')) {
            hideSidebar();
        }
    });

    // Ejecutar la lógica de resize al cargar la página
    if (window.innerWidth < 600 && !sidebar.classList.contains('show')) {
        hideSidebar();
    }
}

// Código para el candado en login.php
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const lockIcon = document.getElementById('lockIcon');

    // Depuración
    console.log('passwordInput:', passwordInput);
    console.log('lockIcon:', lockIcon);

    if (passwordInput && lockIcon) {
        passwordInput.addEventListener('input', function() {
            console.log('Password input value:', passwordInput.value); // Depuración
            // Soporte para Font Awesome 5 y 6
            const isFontAwesome6 = lockIcon.classList.contains('fa-solid');
            const lockClass = isFontAwesome6 ? 'fa-solid fa-lock' : 'fas fa-lock';
            const lockOpenClass = isFontAwesome6 ? 'fa-solid fa-lock-open' : 'fas fa-lock-open';

            if (passwordInput.value.length > 0) {
                lockIcon.className = lockOpenClass + ' lock-icon'; // Reemplazar todas las clases
                console.log('Candado abierto:', lockIcon.className);
            } else {
                lockIcon.className = lockClass + ' lock-icon'; // Reemplazar todas las clases
                console.log('Candado cerrado:', lockIcon.className);
            }
        });
    } else {
        console.error('No se encontraron los elementos passwordInput o lockIcon');
    }
});

// Sign up botón select file noticia
// Verificar envío del formulario
const signupForm = document.getElementById('signupForm');
if (signupForm) {
    signupForm.addEventListener('submit', function(e) {
        console.log('Formulario enviado');
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.add('was-validated');
            console.log('Validación fallida');
            return;
        }
        console.log('Validación exitosa, enviando...');
    });
}

// Verificar clic en el botón
const submitButton = document.getElementById('submitButton');
if (submitButton) {
    submitButton.addEventListener('click', function(e) {
        console.log('Botón submit clickeado');
    });
}

// Función unificada para alternar la visibilidad de la contraseña
function togglePasswordVisibility(inputId, iconElement) {
    const input = document.getElementById(inputId);
    const icon = iconElement.querySelector('i') || iconElement; // Soporta ambos casos: <span> con <i> o directamente el ícono

    if (input && icon) {
        if (input.type === 'password') {
            input.type = 'text';
            if (icon.classList.contains('fa-eye')) {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                icon.innerHTML = '<i class="fas fa-eye-slash"></i>'; // Para manage-users y profile
            }
        } else {
            input.type = 'password';
            if (icon.classList.contains('fa-eye-slash')) {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                icon.innerHTML = '<i class="fas fa-eye"></i>'; // Para manage-users y profile
            }
        }
    }
}

// Cerrar alertas automáticamente después de 5 segundos (para manage-users.php)
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    if (alerts.length > 0 && typeof $ !== 'undefined' && $.fn.alert) {
        setTimeout(function() {
            alerts.forEach(alert => {
                $(alert).alert('close');
            });
        }, 5000);
    }
});

// Add Post
const floatingImage = document.getElementById('floatingImage');
if (floatingImage) {
    floatingImage.addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'Seleccionar archivo';
        const label = document.querySelector('label[for="floatingImage"] span');
        label.textContent = fileName;
    });
}

// Edit Post
const imagen = document.getElementById('imagen');
if (imagen) {
    imagen.addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'Seleccionar archivo';
        const label = document.querySelector('#imagen + div span');
        label.textContent = fileName;
    });
}

// Manage Users
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-user-btn');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');

            Swal.fire({
                title: '¿Estás seguro?',
                text: `Estás seguro de eliminar al usuario ${name}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `<?= BASE_URL ?>/?url=admin/delete/delete-user&id=${id}`;
                }
            });
        });
    });
});
