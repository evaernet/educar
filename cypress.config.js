// Configuración de Cypress (pruebas de punta a punta, simulando a un usuario real).
// Qué hace: indica la dirección donde corre el sistema para las pruebas y dónde están los archivos de prueba.
// Conexión: las pruebas usan una base de datos aparte (database/cypress.sqlite, definida en .env.cypress)
// para no tocar los datos reales de educar_db.
import { defineConfig } from 'cypress';
import fs from 'node:fs';
import { execSync } from 'node:child_process';

// Si la base de datos de pruebas no existe, se crea vacía (las migraciones la completan).
const baseDePrueba = 'database/cypress.sqlite';
if (!fs.existsSync(baseDePrueba)) {
    fs.writeFileSync(baseDePrueba, '');
}

export default defineConfig({
    e2e: {
        // El sistema tiene que estar corriendo con: php artisan serve --env=cypress --port=8001
        baseUrl: 'http://127.0.0.1:8001',
        specPattern: 'cypress/e2e/**/*.cy.js',
        supportFile: false,
        video: false,
        defaultCommandTimeout: 10000,
        taskTimeout: 120000,
        setupNodeEvents(on) {
            // Tarea "prepararBase": deja la base de pruebas limpia (borra todo, crea las tablas y carga el admin).
            on('task', {
                prepararBase() {
                    execSync('php artisan migrate:fresh --seed --env=cypress --force', { stdio: 'inherit' });
                    return null;
                },
            });
        },
    },
});
