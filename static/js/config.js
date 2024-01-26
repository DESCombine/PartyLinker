const local_request_path = 'http://localhost/php/requests';
const remote_request_path = 'https://api.partylinker.live';

export const request_path = location.hostname === "localhost" ? local_request_path : remote_request_path;
console.log(request_path)