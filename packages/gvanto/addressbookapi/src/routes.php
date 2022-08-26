<?php
// For simplicity no Passport/auth used

Route::prefix('addressbookapi')->namespace('gvanto\addressbookapi')->group(function (): void {
    Route::get('/test', 'AddressBookApiController@test');

    Route::post('/persons/add', 'AddressBookApiController@addPerson');

    // Could use route-model binding here too eg /persons/{person}/groups
    Route::get('/persons/groups', 'AddressBookApiController@getPersonGroups');

    Route::get('/persons/find-by-name', 'AddressBookApiController@findPersonByName');

    Route::get('/persons/find-by-email', 'AddressBookApiController@findPersonByEmail');

    Route::post('/groups/add', 'AddressBookApiController@addGroup');

    Route::get('/groups/members', 'AddressBookApiController@getGroupMembers');
});
