const crypto = require("crypto");

// Función para generar el hash SHA-256 de la MAC
function hashMAC(mac) {
    return crypto.createHash("sha256").update(mac).digest("hex");
}

// API para registrar dispositivos
app.post("/api/dispositivos", async (req, res) => {
    const { usuario_id, nombre, numero_serie, mac_address } = req.body;

    if (!mac_address) {
        return res.status(400).json({ message: "La dirección MAC es obligatoria" });
    }

    try {
        const macHash = hashMAC(mac_address);

        // Verificar si la MAC ya está registrada
        const [rows] = await db.query("SELECT * FROM dispositivos WHERE mac_address_hash = ?", [macHash]);
        if (rows.length > 0) {
            return res.status(400).json({ message: "Este dispositivo ya está registrado" });
        }

        // Insertar en la base de datos
        await db.query("INSERT INTO dispositivos (usuario_id, nombre, numero_serie, mac_address, mac_address_hash) VALUES (?, ?, ?, ?, ?)", 
        [usuario_id, nombre, numero_serie, mac_address, macHash]);

        res.json({ message: "Dispositivo registrado correctamente" });

    } catch (error) {
        res.status(500).json({ message: "Error en el servidor", error });
    }
});
