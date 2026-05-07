<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'TestController::index');
$routes->post('/Test/saveTemplate', 'TestController::saveTemplate');
$routes->post('Test/deleteTemplate/(:num)', 'TestController::deleteTemplate/$1');
$routes->post('Test/updateTest/(:num)', 'TestController::updateTest/$1');
$routes->post('Test/createTest', 'TestController::createTest');
$routes->post('Test/deleteTest/(:num)', 'TestController::deleteTest/$1');
$routes->post('Test/createTestPack', 'TestController::createTestPack');
$routes->post('Test/deletePack/(:num)', 'TestController::deletePack/$1');
$routes->post('Test/uploadQuestions', 'TestController::uploadQuestions');
$routes->post('Test/saveQuestion', 'TestController::saveQuestion');
$routes->post('Test/saveQuestionBank', 'TestController::saveQuestionBank');
$routes->post('Test/saveQBQuestion', 'TestController::saveQBQuestion');
$routes->post('Test/updateQBQuestion/(:num)', 'TestController::updateQBQuestion/$1');
$routes->post('Test/deleteQBQuestion/(:num)', 'TestController::deleteQBQuestion/$1');
$routes->post('Test/updateTestPackTemplate', 'TestController::updateTestPackTemplate');
$routes->get('/Test/downloadTemplate/(:any)', 'TestController::downloadTemplate/$1');
$routes->get('Test/getPackQuestions/(:num)', 'TestController::getPackQuestions/$1');
$routes->get('Test/downloadTemplateByTemplateId/(:num)', 'TestController::downloadTemplateByTemplateId/$1');
$routes->post('Test/bulkSaveQBQuestions', 'TestController::bulkSaveQBQuestions');
$routes->post('Test/deleteQuestionBank/(:num)', 'TestController::deleteQuestionBank/$1');
$routes->get('Test/getTests', 'TestController::getTests');
$routes->post('Test/publishTestPack', 'TestController::publishTestPack');
