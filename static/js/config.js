/**
 * This file is necessary to be able to develop and have 
 * a site working both in local and on the server.
 */
const local_request_path = 'http://localhost/php/requests';
const remote_request_path = 'https://api.partylinker.live';

export const request_path = location.hostname === "localhost" ? local_request_path : remote_request_path;