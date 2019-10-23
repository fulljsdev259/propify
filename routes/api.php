<?php

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login')->name('login');
    Route::post('autologin', 'AuthController@autologin');

    // private routes
    Route::middleware('auth:api')->group(function () {
        Route::get('/logout', 'AuthController@logout')->name('logout');
        // Route::get('/me', 'AuthController@user')->name('me');
    });
});

// Constants
Route::get('/constants', 'UtilsAPIController@constants')->name('constants');
Route::put('/residents/resetpassword', 'ResidentAPIController@resetPassword');
Route::post('/residents/activateResident', 'ResidentAPIController@activateResident');

// private routes
Route::middleware('auth:api', 'throttle:180,1', 'locale')->group(function () {
    // Users
    Route::get('/users', 'UserAPIController@index')->name('users');
    Route::get('/users/me', 'UserAPIController@showLoggedIn')->name('users.me');
    Route::get('/users/requestManagers', 'UserAPIController@requestManagers')->name('users.requestManagers'); // @TODO used or not
    Route::get('/users/check-email', 'UserAPIController@checkEmail')->name('users.check-email');
    Route::get('/users/{id}', 'UserAPIController@show')->name('users.show');

    Route::post('/users', 'UserAPIController@store')->name('users.store');
    Route::post('/users/me/upload_image', 'UserAPIController@uploadImageLoggedIn')->name('users.me.upload.image');  // @TODO ROLE RELATED is incorrect permission
    Route::post('/users/{id}/upload_image', 'UserAPIController@uploadImage')->name('users.upload.image'); // @TODO ROLE RELATED one is incorrect permission
    Route::put('/users/me', 'UserAPIController@updateLoggedIn')->name('users.me.update');
    Route::put('/users/me/changePassword', 'UserAPIController@changePassword')->name('users.me.changePassword');
    Route::put('/users/me/settings', 'UserSettingsAPIController@updateLoggedIn')->name('users.me.settings.update');

    Route::put('/users/{id}/settings', 'UserSettingsAPIController@update')->name('users.settings.update');
    Route::put('/users/{id}', 'UserAPIController@update')->name('users.update');

    Route::delete('/users/{id}', 'UserAPIController@destroy')->name('users.destroy');
    Route::post('/users/deletewithids', 'UserAPIController@destroyWithIds')->name('users.destroyWithIds');

    // Residents
    Route::get('/residents', 'ResidentAPIController@index')->name('residents');
    Route::get('/residents/gender-statistics', 'DashboardAPIController@residentsGenderStatistics')->name('residents.gender-statistics');
    Route::get('/residents/age-statistics', 'DashboardAPIController@residentsAgeStatistics')->name('residents.age-statistics');
    Route::get('/residents/latest', 'ResidentAPIController@latest')->name('residents.latest');
    Route::get('/residents/me', 'ResidentAPIController@showLoggedIn')->name('residents.me');
    Route::get('/residents/{id}', 'ResidentAPIController@show')->name('residents.show');
    Route::get('/residents/{id}/statistics', 'DashboardAPIController@residentStatistics')->name('residents.statistics.show');

    Route::post('/residents', 'ResidentAPIController@store')->name('residents.store');
    Route::post('/addReview', 'ResidentAPIController@addReview');
    Route::post('/residents/{id}/send-credentials', 'ResidentAPIController@sendCredentials');
    Route::post('/residents/{id}/download-credentials', 'ResidentAPIController@downloadCredentials');

    Route::put('/residents/default-contract', 'ResidentAPIController@updateDefaultContract')->name('residents.me.update.default-contract');
    Route::put('/residents/me', 'ResidentAPIController@updateLoggedIn')->name('residents.me.update');
    Route::put('/residents/{id}', 'ResidentAPIController@update')->name('residents.update');
    Route::put('/residents/{id}/status', 'ResidentAPIController@changeStatus')->name('residents.changeStatus');

    Route::delete('/residents/{id}', 'ResidentAPIController@destroy')->name('residents.destroy');
    Route::post('/residents/deletewithids', 'ResidentAPIController@destroyWithIds')->name('residents.destroyWithIds');


    //Contract
    Route::get('/contracts', 'ContractAPIController@index')->name('contracts');
    Route::get('/contracts/{id}', 'ContractAPIController@show')->name('contracts.show');
    Route::post('/contracts', 'ContractAPIController@store')->name('contracts.store');
    Route::post('/contracts/{id}/media', 'MediaAPIController@contractUpload')->name('contracts.media.upload');
    Route::put('/contracts/{id}', 'ContractAPIController@update')->name('contracts.update');
    Route::delete('/contracts/{id}', 'ContractAPIController@destroy')->name('contracts.destroy');
    Route::post('/contracts/deletewithids', 'ContractAPIController@destroyWithIds')->name('contracts.destroyWithIds');
    Route::delete('/contracts/{id}/media/{media_id}', 'MediaAPIController@contractDestroy')->name('contracts.media.destroy');

    // Location
    Route::get('/states', 'StateAPIController@index')->name('states');
    Route::get('/countries', 'CountryAPIController@index')->name('countries');

    Route::get('/addresses', 'AddressAPIController@index')->name('addresses');
    Route::get('/addresses/{id}', 'AddressAPIController@show')->name('addresses.show');

    Route::post('/addresses', 'AddressAPIController@store')->name('addresses.store');

    Route::put('/addresses/{id}', 'AddressAPIController@update')->name('addresses.update');

    Route::delete('/addresses/{id}', 'AddressAPIController@destroy')->name('addresses.destroy');

    // Buildings
    Route::get('/buildings', 'BuildingAPIController@index')->name('buildings');
    Route::get('/buildings/latest', 'BuildingAPIController@latest')->name('buildings.latest');
    Route::get('/buildings/map', 'BuildingAPIController@map')->name('buildings.map');
    Route::get('/buildings/{id}', 'BuildingAPIController@show')->name('buildings.show');
    Route::get('/buildings/{id}/statistics', 'DashboardAPIController@buildingStatistics')->name('buildings.statistics.show');
    Route::get('/buildings/{id}/assignees', 'BuildingAPIController@getAssignees');

    Route::post('/buildings', 'BuildingAPIController@store')->name('buildings.store');
    Route::post('/buildings/{id}/media', 'MediaAPIController@buildingUpload')->name('buildings.media.upload');
    Route::post('/buildings/{id}/propertyManagers', 'BuildingAPIController@assignManagers')->name('buildings.assign.managers'); // @TODO delete
    Route::post('/buildings/{id}/managers', 'BuildingAPIController@assignManagers')->name('buildings.assign.managers');
    Route::post('/buildings/{id}/users', 'BuildingAPIController@assignUsers')->name('buildings.assign.users');
    Route::post('/buildings/deletewithids', 'BuildingAPIController@destroyWithIds')->name('buildings.destroyWithIds');
    Route::post('/buildings/checkunitrequest', 'BuildingAPIController@checkUnitRequest')->name('buildings.checkUnitRequest');

    Route::put('/buildings/{id}', 'BuildingAPIController@update')->name('buildings.update');

    Route::delete('/buildings/{id}', 'BuildingAPIController@destroy')->name('buildings.destroy');
    Route::delete('/buildings/{building_id}/media/{media_id}', 'MediaAPIController@buildingDestroy')->name('buildings.media.destroy');
    Route::delete('/buildings/{building_id}/service/{service_id}', 'BuildingAPIController@serviceRemove')->name('buildings.service.destroy');
    Route::delete('/buildings/{building_id}/propertyManagers/{manager_id}', 'BuildingAPIController@unAssignPropertyManager')->name('buildings.manager.destroy');
    Route::delete('/buildings-assignees/{buildings_assignee_id}', 'BuildingAPIController@deleteBuildingAssignee');


    // Units
    Route::get('/units', 'UnitAPIController@index')->name('units');
    Route::get('/units/{id}', 'UnitAPIController@show')->name('units.show');
    Route::post('/units', 'UnitAPIController@store')->name('units.store');
    Route::post('/units/{id}/media', 'MediaAPIController@unitUpload')->name('units.media.upload');
    Route::put('/units/{id}', 'UnitAPIController@update')->name('units.update');
    Route::delete('/units/{id}', 'UnitAPIController@destroy')->name('units.destroy');
    Route::post('/units/deletewithids', 'UnitAPIController@destroyWithIds')->name('units.destroyWithIds');
    Route::post('/units/{id}/assignees/{assignee_id}', 'UnitAPIController@assignResident');
    Route::delete('/units/{id}/assignees/{assignee_id}', 'UnitAPIController@unassignResident');
    Route::delete('/units/{unit_id}/media/{media_id}', 'MediaAPIController@unitDestroy')->name('units.media.destroy');

    // Settings
    Route::get('/settings', 'SettingsAPIController@show')->name('settings.show');
    Route::put('/settings', 'SettingsAPIController@update')->name('settings.update');

    // Services
    Route::get('/services', 'ServiceProviderAPIController@index')->name('services');
    Route::get('/services/category', 'ServiceProviderAPIController@indexByCategory')->name('services.byCategory');
    Route::get('/services/{id}', 'ServiceProviderAPIController@show')->name('services.show');
    Route::post('/services', 'ServiceProviderAPIController@store')->name('services.store');
    Route::put('/services/{id}', 'ServiceProviderAPIController@update')->name('services.update');
    Route::delete('/services/{id}', 'ServiceProviderAPIController@destroy')->name('services.destroy');
    Route::post('/services/deletewithids', 'ServiceProviderAPIController@destroyWithIds')->name('services.destroyWithIds');
    Route::post('/services/{id}/quarters/{quarter_id}', 'ServiceProviderAPIController@assignQuarter');
    Route::delete('/services/{id}/quarters/{quarter_id}', 'ServiceProviderAPIController@unassignQuarter');
    Route::post('/services/{id}/buildings/{building_id}', 'ServiceProviderAPIController@assignBuilding');
    Route::delete('/services/{id}/buildings/{building_id}', 'ServiceProviderAPIController@unassignBuilding');
    Route::get('/services/{id}/locations', 'ServiceProviderAPIController@getLocations');

    // Quarters
    Route::get('/quarters', 'QuarterAPIController@index')->name('quarters');
    Route::get('/quarters/{id}', 'QuarterAPIController@show')->name('quarters.show');
    Route::post('/quarters', 'QuarterAPIController@store')->name('quarters.store');
    Route::put('/quarters/{id}', 'QuarterAPIController@update')->name('quarters.update');
    Route::delete('/quarters/{id}', 'QuarterAPIController@destroy')->name('quarters.destroy');
    Route::post('/quarters/deletewithids', 'QuarterAPIController@destroyWithIds')->name('quarters.destroyWithIds');
    Route::post('/quarters/{id}/media', 'MediaAPIController@quarterUpload')->name('quarters.media.upload');

    Route::get('/quarters/{id}/assignees', 'QuarterAPIController@getAssignees');
    Route::post('/quarters/{id}/managers', 'QuarterAPIController@assignManagers')->name('quarters.assign.managers'); // @TODO delete
    Route::post('/quarters/{id}/users', 'QuarterAPIController@assignUsers')->name('quarters.assign.users');
    Route::delete('/quarters-assignees/{quarters_assignee_id}', 'QuarterAPIController@deleteQuarterAssignee');
    Route::delete('/quarters/{quarter_id}/media/{media_id}', 'MediaAPIController@quarterDestroy')->name('quarters.media.destroy');

    // Pinboard
    Route::get('pinboard/rss.xml', 'PinboardAPIController@showNewsRSS');
    Route::get('pinboard/weather.json', 'PinboardAPIController@showWeatherJSON');

    Route::post('/pinboard/deletewithids', 'PinboardAPIController@destroyWithIds')->name('pinboard.destroyWithIds');
    Route::post('pinboard/{id}/publish', 'PinboardAPIController@publish')->name('pinboard.publish');
    Route::post('pinboard/{id}/like', 'PinboardAPIController@like')->name('pinboard.like');
    Route::post('pinboard/{id}/unlike', 'PinboardAPIController@unlike')->name('pinboard.unlike');
    Route::post('pinboard/{id}/media', 'MediaAPIController@pinboardUpload')->name('pinboard.media.upload');
    Route::delete('pinboard/{id}/media/{media_id}', 'MediaAPIController@pinboardDestroy')->name('pinboard.media.destroy');
    Route::post('pinboard/{id}/comments', 'CommentAPIController@storePinboardComment')->name('pinboard.store.comment');
    Route::get('/pinboard/{id}/locations', 'PinboardAPIController@getLocations');
    Route::post('/pinboard/{id}/buildings/{building_id}', 'PinboardAPIController@assignBuilding');
    Route::delete('/pinboard/{id}/buildings/{building_id}', 'PinboardAPIController@unassignBuilding');
    Route::post('/pinboard/{id}/quarters/{quarter_id}', 'PinboardAPIController@assignQuarter');
    Route::delete('/pinboard/{id}/quarters/{quarter_id}', 'PinboardAPIController@unassignQuarter');
    Route::post('/pinboard/{id}/providers/{provider_id}', 'PinboardAPIController@assignProvider');
    Route::delete('/pinboard/{id}/providers/{provider_id}', 'PinboardAPIController@unassignProvider');
    Route::put('/pinboard/{id}/views', 'PinboardAPIController@incrementViews');
    Route::get('/pinboard/{id}/views', 'PinboardAPIController@indexViews');

    Route::resource('pinboard', 'PinboardAPIController');

    //Internal Notices
    Route::resource('internalNotices', 'InternalNoticeAPIController');

    // Comments & Notifications & Conversations
    Route::get('/comments', 'CommentAPIController@index')->name('comments');
    Route::get('/comments/{id}', 'CommentAPIController@children')->name('comments.children');
    Route::put('/comments/{id}', 'CommentAPIController@updateComment')->name('comments.update');
    Route::delete('/comments/{id}', 'CommentAPIController@destroyComment')->name('comments.destroy');
    Route::get('/notifications', 'NotificationAPIController@index')->name('notifications');
    Route::post('/notifications', 'NotificationAPIController@markAllAsRead')->name('notifications.markAll');
    Route::post('/notifications/{id}', 'NotificationAPIController@markAsReadUnRead')->name('notifications.mark');
    Route::get('/conversations', 'ConversationAPIController@index');
    Route::post('/conversations/{id}/comments', 'ConversationAPIController@storeComment');

    // Cleanify Request
    Route::get('cleanify', 'CleanifyRequestAPIController@index');
    Route::post('cleanify', 'CleanifyRequestAPIController@store');

    // Service Requests Category
    Route::get('/requestCategories', 'RequestCategoryAPIController@index')->name('requests.categories');
    Route::get('/requestCategories/tree', 'RequestCategoryAPIController@categoryTree')->name('requests.categories.tree');
    Route::get('/requestCategories/{id}', 'RequestCategoryAPIController@show')->name('requests.categories.show');
    Route::post('/requestCategories', 'RequestCategoryAPIController@store')->name('requests.categories.store');
    Route::put('/requestCategories/{id}', 'RequestCategoryAPIController@update')->name('requests.categories.update');
    Route::delete('/requestCategories/{id}', 'RequestCategoryAPIController@destroy')->name('requests.categories.destroy');

    // Tag Requests
    Route::resource('tags', 'TagAPIController');

    // Service Requests
    Route::get('/requests', 'RequestAPIController@index')->name('requests');
    Route::get('/requestsCounts', 'RequestAPIController@requestsCounts')->name('requestsCounts');
    Route::get('/requests/statistics', 'DashboardAPIController@requestsStatistics')->name('requests.statistics');
    Route::get('/requests/{id}', 'RequestAPIController@show')->name('requests.show');
    Route::post('/requests', 'RequestAPIController@store')->name('requests.store');
    Route::post('/requests/{id}/media', 'MediaAPIController@requestUpload')->name('requests.media.upload');
    Route::post('/requests/{id}/comments', 'CommentAPIController@storeRequestComment')->name('requests.comment.store');
    Route::post('/requests/{id}/notify', 'RequestAPIController@notifyProvider')->name('requests.notify');
    Route::put('/requests/massedit', 'RequestAPIController@massEdit')->name('requests.mass-edit');
    Route::put('/requests/{id}', 'RequestAPIController@update')->name('requests.update');
    Route::put('/requests/{id}/status', 'RequestAPIController@changeStatus')->name('requests.changeStatus');
//    Route::put('/requests/{id}/priority', 'RequestAPIController@changePriority')->name('requests.changePriority');
    Route::delete('/requests/{id}', 'RequestAPIController@destroy')->name('requests.destroy');
    Route::post('/requests/deletewithids', 'RequestAPIController@destroyWithIds')->name('requests.destroyWithIds');
    Route::delete('/requests/{id}/media/{media_id}', 'MediaAPIController@requestDestroy')->name('requests.media.destroy');
    Route::post('/requests/{id}/download-pdf', 'RequestAPIController@downloadPdf');

    Route::get('/requests/{id}/tags', 'RequestAPIController@getTags');
    Route::get('/requests/{id}/assignees', 'RequestAPIController@getAssignees');
    Route::post('/requests/{id}/users/{user_id}', 'RequestAPIController@assignUser');
    Route::post('/requests/{id}/providers/{provider_id}', 'RequestAPIController@assignProvider');
    Route::post('/requests/{id}/managers/{manager_id}', 'RequestAPIController@assignManager');
    Route::post('/requests/{id}/tags', 'RequestAPIController@assignManyTags')->name('request.assign.many-tags');
    Route::post('/requests/{id}/tags/{tag_id}', 'RequestAPIController@assignTag');
    Route::delete('/requests/{id}/tags', 'RequestAPIController@unassignManyTags')->name('request.unassign.many-tags');
    Route::delete('/requests/{id}/tags/{tag_id}', 'RequestAPIController@unassignTag');
    Route::delete('/requests-assignees/{requests_assignee_id}', 'RequestAPIController@deleteRequestAssignee');

    // @TODO remove later
    Route::post('/requests/{id}/assignees/{assignee_id}', 'RequestAPIController@assignTmpManager');
    Route::delete('/requests/{id}/assignees/{assignee_id}', 'RequestAPIController@unassignUser');
    Route::delete('/requests/{id}/providers/{provider_id}', 'RequestAPIController@unassignProvider');


    Route::get('/requests/{id}/communicationTemplates', 'RequestAPIController@getCommunicationTemplates');
    Route::get('/requests/{id}/serviceCommunicationTemplates', 'RequestAPIController@getServiceCommunicationTemplates');
    Route::get('/requests/{id}/serviceEmailTemplates', 'RequestAPIController@getServiceEmailTemplates');


    // Listings
//    Route::resource('listings', 'ListingAPIController');
//    Route::post('listings/{id}/like', 'ListingAPIController@like')->name('listings.like');
//    Route::post('listings/{id}/unlike', 'ListingAPIController@unlike')->name('listings.unlike');
//    Route::post('listings/{id}/media', 'MediaAPIController@listingUpload')->name('listings.media.upload');
//    Route::delete('listings/{id}/media/{media_id}', 'MediaAPIController@listingDestroy')->name('listings.media.destroy');
//    Route::post('/listings/deletewithids', 'ListingAPIController@destroyWithIds')->name('listings.destroyWithIds');
//    Route::post('listings/{id}/comments', 'CommentAPIController@storeProductComment')->name('listings.store.comment');
//    Route::post('listings/{id}/publish', 'ListingAPIController@publish')->name('listings.publish');

    // Service Requests
    Route::get('propertyManagers', 'PropertyManagerAPIController@index')->name('propertyManagers');
    Route::get('propertyManagers/{id}', 'PropertyManagerAPIController@show')->name('propertyManagers.show');
    Route::get('propertyManagers/{id}/assignments', 'PropertyManagerAPIController@getAssignments');
    Route::post('propertyManagers/idsassignments', 'PropertyManagerAPIController@getIDsAssignmentsCount');
    Route::post('propertyManagers', 'PropertyManagerAPIController@store')->name('propertyManagers.store');
    Route::put('propertyManagers/{id}', 'PropertyManagerAPIController@update')->name('propertyManagers.update');
    Route::delete('/propertyManagers/batchDelete', 'PropertyManagerAPIController@batchDelete');
    Route::delete('propertyManagers/{id}', 'PropertyManagerAPIController@destroy')->name('propertyManagers.destroy');
    Route::post('/propertyManagers/{id}/quarters/{quarter_id}', 'PropertyManagerAPIController@assignQuarter');
    Route::delete('/propertyManagers/{id}/quarters/{quarter_id}', 'PropertyManagerAPIController@unassignQuarter');
    Route::post('/propertyManagers/{id}/buildings/{building_id}', 'PropertyManagerAPIController@assignBuilding');
    Route::delete('/propertyManagers/{id}/buildings/{building_id}', 'PropertyManagerAPIController@unassignBuilding');

    // Templates
    Route::get('/templates', 'TemplateAPIController@index')->name('templates');
    Route::get('/templates/categories', 'TemplateAPIController@categories')->name('templates.categories');
    Route::get('/templates/{id}', 'TemplateAPIController@show')->name('templates.show');
    Route::post('/templates', 'TemplateAPIController@store')->name('templates.store');
    Route::put('/templates/{id}', 'TemplateAPIController@update')->name('templates.update');
    Route::delete('/templates/{id}', 'TemplateAPIController@destroy')->name('templates.destroy');

    // Audits
    Route::get('/audits', 'AuditAPIController@index');

    // Translations
    Route::resource('translations', 'TranslationAPIController');
    Route::get('/admin/statistics', 'DashboardAPIController@adminStats');
    Route::get('/admin/chartRequestByCreationDate', 'DashboardAPIController@chartRequestByCreationDate');
    Route::get('/admin/chartRequestByAssignedProvider', 'DashboardAPIController@chartRequestByAssignedProvider');
    Route::get('/admin/chartBuildingsByCreationDate', 'DashboardAPIController@chartBuildingsByCreationDate');
    Route::get('/admin/chartByCreationDate', 'DashboardAPIController@chartByCreationDate');

    Route::get('/admin/donutChart', 'DashboardAPIController@donutChart');
    Route::get('/admin/donutChartRequestByCategory', 'DashboardAPIController@donutChartRequestByCategory');
    Route::get('/admin/donutChartResidentsByDateAndStatus', 'DashboardAPIController@donutChartResidentsByDateAndStatus');
    Route::get('/admin/pieChartBuildingByState', 'DashboardAPIController@pieChartBuildingByState');

    Route::get('/admin/heatMapByDatePeriod', 'DashboardAPIController@heatMapByDatePeriod');
    Route::get('/admin/chartLoginDevice', 'DashboardAPIController@chartLoginDevice');
    Route::get('/admin/chartResidentLanguage', 'DashboardAPIController@chartResidentLanguage');
});


