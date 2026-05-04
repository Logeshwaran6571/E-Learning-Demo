<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AssessmentController::index');
$routes->post('/assessment/saveTemplate', 'AssessmentController::saveTemplate');
$routes->post('assessment/deleteTemplate/(:num)', 'AssessmentController::deleteTemplate/$1');
$routes->post('assessment/updateAssessment/(:num)', 'AssessmentController::updateAssessment/$1');
$routes->post('assessment/createAssessment', 'AssessmentController::createAssessment');
$routes->post('assessment/deleteAssessment/(:num)', 'AssessmentController::deleteAssessment/$1');
$routes->post('assessment/createTestPack', 'AssessmentController::createTestPack');
$routes->post('assessment/deletePack/(:num)', 'AssessmentController::deletePack/$1');
$routes->post('assessment/uploadQuestions', 'AssessmentController::uploadQuestions');
$routes->get('/assessment/downloadTemplate/(:any)', 'AssessmentController::downloadTemplate/$1');
