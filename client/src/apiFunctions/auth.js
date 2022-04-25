import axios from "axios";

export function login(credentials, setToken) {
    axios.post('http://127.0.0.1:8000/api/login', {...credentials})
        .then((res) => setToken({success: true, token: res}))
        .catch((e) => setToken({success: false, message: e.message}))
}

export function getRole(token, setRole) {
    axios.get('http://127.0.0.1:8000/api/role', {headers: {'Authorization': "Bearer " + token.token.data.token}})
        .then((res) => setRole(res))
        .catch((e) => setRole(null))
}