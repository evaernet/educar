// Prueba de regresión: Transporte (RF18) - un recorrido no puede superar su cupo.
// Qué hace: simula al administrador en el navegador. Crea dos alumnos y un recorrido con cupo 1,
// inscribe al primero y verifica que el sistema rechace al segundo porque el recorrido está lleno.
// Conexión: usa las pantallas reales de /login, /admin/alumnos y /admin/transporte.
// Es de regresión porque se vuelve a correr después de cada cambio para comprobar que la regla
// del cupo sigue funcionando y que ningún cambio nuevo la rompió.

const admin = { email: 'admin@educar.com', password: 'admin1234' };

const alumnos = [
    { legajo: '9001', dni: '40111222', nombre: 'Ana', apellido: 'Prueba' },
    { legajo: '9002', dni: '40333444', nombre: 'Bruno', apellido: 'Prueba' },
];

function iniciarSesionComoAdmin() {
    cy.visit('/login');
    cy.get('input[name="email"]').type(admin.email);
    cy.get('input[name="password"]').type(admin.password);
    cy.contains('button', 'Ingresar').click();
    cy.url().should('include', '/admin');
}

function registrarAlumno(alumno) {
    cy.visit('/admin/alumnos/create');
    cy.get('#legajo').type(alumno.legajo);
    cy.get('#dni').type(alumno.dni);
    cy.get('#nombre').type(alumno.nombre);
    cy.get('#apellido').type(alumno.apellido);
    cy.get('#fecha_nacimiento').type('2012-05-10');
    cy.get('#domicilio').type('Calle Falsa 123');
    cy.contains('button', 'Guardar alumno').click();
    cy.url().should('not.include', '/create');
}

function inscribir(alumno) {
    cy.get('select[name="alumno_id"]').select(`${alumno.apellido}, ${alumno.nombre}`);
    cy.contains('select[name="recorrido_transporte_id"] option', 'Recorrido Cypress')
        .invoke('val')
        .then((id) => cy.get('select[name="recorrido_transporte_id"]').select(id));
    cy.contains('button', 'Inscribir').click();
}

describe('Transporte - cupo de los recorridos (RF18)', () => {
    before(() => {
        // Deja la base de pruebas limpia: borra todo, crea las tablas y carga el admin de prueba.
        cy.task('prepararBase');
    });

    it('no permite inscribir más alumnos que el cupo del recorrido', () => {
        iniciarSesionComoAdmin();
        alumnos.forEach(registrarAlumno);

        // Paso 1: crear un recorrido con cupo para 1 solo alumno.
        cy.visit('/admin/transporte');
        cy.get('input[name="nombre"]').type('Recorrido Cypress');
        cy.get('input[name="zona"]').type('Zona Norte');
        cy.get('input[name="cupo"]').type('1');
        cy.contains('button', 'Guardar recorrido').click();
        cy.contains('Recorrido de transporte registrado correctamente.').should('be.visible');

        // Paso 2: el primer alumno entra (queda el recorrido lleno: 1/1).
        inscribir(alumnos[0]);
        cy.contains('Alumno inscripto al transporte correctamente.').should('be.visible');

        // Paso 3: el segundo alumno tiene que ser rechazado.
        inscribir(alumnos[1]);
        cy.contains('El recorrido seleccionado no tiene cupos disponibles.').should('be.visible');

        // Paso 4: en la tabla de inscripciones activas solo figura el primer alumno.
        cy.get('table tbody tr').should('have.length', 1).and('contain', 'Prueba, Ana');
    });
});
