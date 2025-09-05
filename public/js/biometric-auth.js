class BiometricAuth {
    constructor() {
        this.biometricKey = 'asg_biometric_token';
        this.isAvailable = this.checkBiometricSupport();
    }

    checkBiometricSupport() {
        return (typeof PublicKeyCredential !== 'undefined' && 
               (typeof navigator.credentials !== 'undefined' && 
                typeof navigator.credentials.create !== 'undefined')) || 
               (typeof window.PasswordCredential !== 'undefined') ||
               (typeof window.FederatedCredential !== 'undefined');
    }

    // Registrar credenciales biométricas
    async registerBiometric(email, token) {
        if (!this.isAvailable) {
            return false;
        }

        try {
            // Almacenar token localmente para autenticación futura
            localStorage.setItem(this.biometricKey, JSON.stringify({
                email: email,
                token: token,
                timestamp: new Date().getTime()
            }));

            if (typeof PublicKeyCredential !== 'undefined') {
                // Intentar usar WebAuthn para autenticación biométrica
                const publicKey = {
                    challenge: new Uint8Array(32),
                    rp: { name: "ASG App" },
                    user: {
                        id: new Uint8Array(16),
                        name: email,
                        displayName: email
                    },
                    pubKeyCredParams: [{ type: "public-key", alg: -7 }]
                };

                const credential = await navigator.credentials.create({ publicKey });
                if (credential) {
                    console.log("Credencial biométrica registrada");
                    return true;
                }
            }

            return true;
        } catch (error) {
            console.error("Error registrando autenticación biométrica:", error);
            return false;
        }
    }

    // Autenticar con biométricos
    async authenticate() {
        if (!this.isAvailable) {
            return false;
        }

        const stored = localStorage.getItem(this.biometricKey);
        if (!stored) {
            return false;
        }

        const { email, token } = JSON.parse(stored);

        try {
            if (typeof PublicKeyCredential !== 'undefined') {
                // Intentar autenticación con WebAuthn
                const assertion = await navigator.credentials.get({
                    publicKey: {
                        challenge: new Uint8Array(32),
                        allowCredentials: [{
                            type: 'public-key',
                            id: new Uint8Array(16),
                            transports: ['internal']
                        }],
                        userVerification: 'required'
                    }
                });

                if (assertion) {
                    // Autenticación biométrica exitosa, usar el token almacenado
                    const response = await fetch('/biometric-login', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ biometric_token: token })
                    });

                    const result = await response.json();
                    if (result.success) {
                        window.location.href = result.redirect;
                        return true;
                    }
                }
            } else {
                // Fallback: usar el token directamente
                const response = await fetch('/biometric-login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ biometric_token: token })
                });

                const result = await response.json();
                if (result.success) {
                    window.location.href = result.redirect;
                    return true;
                }
            }
        } catch (error) {
            console.error("Error en autenticación biométrica:", error);
            return false;
        }
    }

    // Verificar si hay credenciales biométricas almacenadas
    hasStoredCredentials() {
        return localStorage.getItem(this.biometricKey) !== null;
    }

    // Eliminar credenciales biométricas
    removeCredentials() {
        localStorage.removeItem(this.biometricKey);
        return true;
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.biometricAuth = new BiometricAuth();
    
    // Mostrar opción biométrica si está disponible
    if (window.biometricAuth.isAvailable) {
        const biometricOption = document.getElementById('biometric-option');
        if (biometricOption) {
            biometricOption.style.display = 'block';
        }
        
        // Si ya hay credenciales almacenadas, mostrar botón de login biométrico
        if (window.biometricAuth.hasStoredCredentials()) {
            const biometricLoginBtn = document.getElementById('biometric-login-btn');
            if (biometricLoginBtn) {
                biometricLoginBtn.style.display = 'block';
                biometricLoginBtn.addEventListener('click', function() {
                    window.biometricAuth.authenticate();
                });
            }
        }
    }
});