class BiometricAuth {
    constructor() {
        this.isAvailable = this.checkBiometricSupport();
    }

    checkBiometricSupport() {
        return (typeof PublicKeyCredential !== "undefined" &&
                typeof navigator.credentials !== "undefined");
    }

    async registerBiometric(email) {
        if (!this.isAvailable) {
            this.showError("Tu dispositivo no soporta autenticación biométrica.");
            return false;
        }

        try {
            const publicKey = {
                challenge: new Uint8Array(32), // normalmente te lo da el servidor
                rp: { name: "ASG App" },
                user: {
                    id: new Uint8Array(16),
                    name: email,
                    displayName: email
                },
                pubKeyCredParams: [{ type: "public-key", alg: -7 }],
                authenticatorSelection: {
                    authenticatorAttachment: "platform", // usa biometría del dispositivo
                    userVerification: "required"
                },
                timeout: 60000,
            };

            const credential = await navigator.credentials.create({ publicKey });

            if (credential) {
                // Guardar en backend la credencial
                const response = await fetch("/biometric-register", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        id: credential.id,
                        rawId: this.arrayBufferToBase64(credential.rawId),
                        type: credential.type,
                    })
                });

                const result = await response.json();
                if (result.success) {
                    this.showSuccess("Biometría registrada correctamente ✅");
                    return true;
                } else {
                    this.showError("Error al registrar credenciales biométricas.");
                }
            }
        } catch (error) {
            console.error("Error registrando biometría:", error);
            this.showError("No se pudo registrar la huella o FaceID.");
            return false;
        }
    }

    async authenticate() {
        if (!this.isAvailable) {
            this.showError("La autenticación biométrica no está disponible.");
            return false;
        }

        try {
            const publicKey = {
                challenge: new Uint8Array(32), // normalmente viene del servidor
                allowCredentials: [], // el servidor debería devolver las credenciales del usuario
                userVerification: "required"
            };

            const assertion = await navigator.credentials.get({ publicKey });

            if (assertion) {
                // Verificar en el backend
                const response = await fetch("/biometric-login", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        id: assertion.id,
                        rawId: this.arrayBufferToBase64(assertion.rawId),
                        type: assertion.type
                    })
                });

                const result = await response.json();
                if (result.success) {
                    window.location.href = result.redirect;
                    return true;
                } else {
                    this.showError("Autenticación fallida.");
                    return false;
                }
            }
        } catch (error) {
            console.error("Error en autenticación biométrica:", error);
            this.showError("No se pudo autenticar con la huella/FaceID.");
            return false;
        }
    }

    arrayBufferToBase64(buffer) {
        let binary = '';
        const bytes = new Uint8Array(buffer);
        const len = bytes.byteLength;
        for (let i = 0; i < len; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        return btoa(binary);
    }

    showError(message) {
        alert("❌ " + message);
    }

    showSuccess(message) {
        alert("✅ " + message);
    }
}

// Inicializar
document.addEventListener("DOMContentLoaded", () => {
    window.biometricAuth = new BiometricAuth();

    const biometricBtn = document.getElementById("biometric-login-btn");
    if (biometricBtn && window.biometricAuth.isAvailable) {
        biometricBtn.style.display = "block";
        biometricBtn.addEventListener("click", () => {
            window.biometricAuth.authenticate();
        });
    }
});
