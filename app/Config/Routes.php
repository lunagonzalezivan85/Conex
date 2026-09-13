<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Portal publico
$routes->get('/', 'PublicController::index');
$routes->get('/buscar-empleo', 'PublicController::buscarEmpleo');
$routes->get('/vacante/(:segment)', 'PublicController::verVacante/$1');
$routes->get('/planes', 'PublicController::planes');
$routes->get('/nosotros', 'PublicController::nosotros');
$routes->get('/noticias', 'NoticiaController::listar');
$routes->get('/noticias/(:segment)', 'NoticiaController::ver/$1');

// Autenticacion
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::attemptLogin');
$routes->get('/registro', 'AuthController::registro');
$routes->post('/registro', 'AuthController::attemptRegistro');
$routes->get('/logout', 'AuthController::logout');
$routes->get('/auth/google', 'AuthController::googleRedirect');
$routes->get('/auth/google/callback', 'AuthController::googleCallback');
$routes->get('/verificar/(:segment)', 'AuthController::verificar/$1');

// Panel candidato
$routes->get('/candidato', 'CandidatoController::index');
$routes->get('/candidato/perfil', 'CandidatoController::perfil');
$routes->post('/candidato/perfil', 'CandidatoController::guardarPerfil');
$routes->post('/candidato/experiencia', 'CandidatoController::guardarExperiencia');
$routes->post('/candidato/educacion', 'CandidatoController::guardarEducacion');
$routes->post('/candidato/habilidades', 'CandidatoController::guardarHabilidades');
$routes->post('/candidato/idiomas', 'CandidatoController::guardarIdiomas');
$routes->post('/candidato/cv/upload', 'CandidatoController::subirCV');
$routes->post('/candidato/cv/eliminar', 'CandidatoController::eliminarCV');
$routes->post('/candidato/cv/analizar', 'CandidatoController::analizarCV');
$routes->post('/candidato/cv/aplicar', 'CandidatoController::aplicarAnalisis');
$routes->get('/candidato/cv/analisis/(:num)', 'CandidatoController::obtenerAnalisis/$1');
$routes->get('/candidato/postulaciones', 'CandidatoController::postulaciones');
$routes->get('/candidato/buscar-empleo', 'CandidatoController::buscarEmpleo');
$routes->get('/candidato/vacante/(:any)', 'CandidatoController::verVacante/$1');
$routes->post('/candidato/postularse/(:num)', 'CandidatoController::postularse/$1');
$routes->get('/candidato/info', 'CandidatoController::info');
$routes->get('/candidato/planes', 'CandidatoController::planes');
$routes->post('/candidato/subir-documento', 'CandidatoController::subirDocumento');

// Panel empresa
$routes->get('/empresa', 'EmpresaController::index');
$routes->get('/empresa/vacantes', 'EmpresaController::vacantes');
$routes->get('/empresa/vacante/ver/(:num)', 'EmpresaController::verVacante/$1');
$routes->get('/empresa/vacante/crear', 'EmpresaController::crearVacante');
$routes->post('/empresa/vacante/crear', 'EmpresaController::guardarVacante');
$routes->post('/empresa/vacante/sugerir', 'EmpresaController::sugerirVacante');
$routes->get('/empresa/vacante/cerrar/(:num)', 'EmpresaController::cerrarVacante/$1');
$routes->get('/empresa/postulantes', 'EmpresaController::postulantes');
$routes->get('/empresa/postulante/ver/(:num)', 'EmpresaController::verPostulante/$1');
$routes->post('/empresa/postulante/estado/(:num)', 'EmpresaController::cambiarEstadoPostulante/$1');
$routes->get('/empresa/perfil', 'EmpresaController::perfil');
$routes->post('/empresa/perfil', 'EmpresaController::guardarPerfil');
$routes->get('/empresa/atencion', 'EmpresaController::atencionCliente');
$routes->post('/empresa/atencion/guardar', 'EmpresaController::guardarIncidencia');
$routes->get('/empresa/encuestas', 'EmpresaController::encuestas');
$routes->get('/empresa/encuestas/form/(:num)', 'EmpresaController::obtenerEncuestaForm/$1');
$routes->post('/empresa/encuestas/guardar/(:num)', 'EmpresaController::guardarEncuesta/$1');
$routes->get('/empresa/info', 'EmpresaController::info');
$routes->get('/empresa/planes', 'EmpresaController::planes');

// Panel admin
$routes->get('/admin', 'AdminController::index');
$routes->get('/admin/usuarios', 'AdminController::usuarios');
$routes->get('/admin/vacantes', 'AdminController::vacantes');
$routes->get('/admin/postulantes', 'AdminController::postulantes');
$routes->get('/admin/postulantes/ver/(:num)', 'AdminController::verificarPostulante/$1');
$routes->get('/admin/postulantes/perfil/(:num)', 'AdminController::perfilPostulante/$1');
$routes->post('/admin/proceso/iniciar', 'AdminController::iniciarProceso');
$routes->post('/admin/proceso/avanzar', 'AdminController::avanzarPaso');
$routes->post('/admin/proceso/retroceder', 'AdminController::retrocederPaso');
$routes->post('/admin/proceso/evaluacion', 'AdminController::guardarEvaluacion');
$routes->post('/admin/proceso/recalcular', 'AdminController::recalcularEvaluacion');
$routes->post('/admin/proceso/presentar', 'AdminController::asignarPresentacion');
$routes->post('/admin/documentos/subir', 'AdminController::subirDocumento');
$routes->post('/admin/documentos/verificar/(:num)', 'AdminController::verificarDocumento/$1');
$routes->get('/admin/info', 'AdminController::info');
$routes->get('/admin/noticias', 'NoticiaController::adminListar');
$routes->get('/admin/noticias/crear', 'NoticiaController::adminCrear');
$routes->post('/admin/noticias/guardar', 'NoticiaController::adminGuardar');
$routes->get('/admin/noticias/editar/(:num)', 'NoticiaController::adminEditar/$1');
$routes->post('/admin/noticias/actualizar/(:num)', 'NoticiaController::adminActualizar/$1');
$routes->get('/admin/noticias/eliminar/(:num)', 'NoticiaController::adminEliminar/$1');

// CRM
$routes->get('/admin/crm', 'CrmController::pipeline');
$routes->post('/admin/crm/crear', 'CrmController::crearLead');
$routes->get('/admin/crm/lead/(:num)', 'CrmController::detalleLead/$1');
$routes->post('/admin/crm/actualizar/(:num)', 'CrmController::actualizarLead/$1');
$routes->post('/admin/crm/cambiar-estado/(:num)', 'CrmController::cambiarEstadoLead/$1');
$routes->post('/admin/crm/seguimiento/(:num)', 'CrmController::agregarSeguimiento/$1');
$routes->post('/admin/crm/servicio/(:num)', 'CrmController::agregarServicio/$1');
$routes->post('/admin/crm/convertir/(:num)', 'CrmController::convertirLead/$1');
