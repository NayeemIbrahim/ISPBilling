const express = require('express');
const router = express.Router();
const PageController = require('../app/Controllers/PageController');
const CustomerController = require('../app/Controllers/CustomerController');

// Define Routes
router.get('/', PageController.home);
router.get('/dashboard', PageController.dashboard);
router.get('/login', PageController.login);

// Customer Routes
router.get('/customers', CustomerController.index);
router.get('/customers/create', CustomerController.createPage);
router.get('/customers/edit/:id', CustomerController.editPage); // New Edit Route
router.get('/customers/search', CustomerController.searchPage);

// API & Actions
router.post('/api/customers', CustomerController.store);
router.post('/api/customers/update/:id', CustomerController.update); // New Update Route
router.post('/api/customers/delete/:id', CustomerController.delete);
router.post('/api/customers/seed', CustomerController.seed);
router.get('/api/customers/search', CustomerController.search);

module.exports = router;
