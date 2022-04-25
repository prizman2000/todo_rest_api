import axios from "axios";

export function getAllBlogs(token, setData) {
    axios.get('http://127.0.0.1:8000/api/group', {headers: {'Authorization': "Bearer " + token.token.data.token}})
        .then((res) => setData(res.data))
        .catch((e) => console.log(e))
}

export function addGroup(group, token, refresh, setRefresh) {
    axios.post('http://127.0.0.1:8000/api/group', {...group}, {headers: {'Authorization': "Bearer " + token.token.data.token}})
        .then((res) => setRefresh(!refresh))
        .catch((e) => console.log(e))
}

export function deleteGroup(groupId, token, refresh, setRefresh) {
    axios.delete(`http://127.0.0.1:8000/api/group/${groupId}`, {headers: {'Authorization': "Bearer " + token.token.data.token}})
        .then((res) => setRefresh(!refresh))
        .catch((e) => console.log(e))
}

export function addPostInGroup(data, token, refresh, setRefresh) {
    axios.post(`http://127.0.0.1:8000/api/group-add-post`, {...data}, {headers: {'Authorization': "Bearer " + token.token.data.token}})
        .then((res) => setRefresh(!refresh))
        .catch((e) => console.log(e))
}

export function getPostsInGroup(groupId, token, setData) {
    axios.get(`http://127.0.0.1:8000/api/group-post/${groupId}`,  {headers: {'Authorization': "Bearer " + token.token.data.token}})
        .then((res) => setData(res.data))
        .catch((e) => console.log(e))
}

export function deletePostFromGroup(postGroupId, token, refresh, setRefresh) {
    axios.delete(`http://127.0.0.1:8000/api/group-delete-post/${postGroupId}`, {headers: {'Authorization': "Bearer " + token.token.data.token}})
        .then((res) => setRefresh(!refresh))
        .catch((e) => console.log(e))
}

export function subscribe(body, token, refresh, setRefresh) {
    axios.post(`http://127.0.0.1:8000/api/subscribe`, {...body}, {headers: {'Authorization': "Bearer " + token.token.data.token}})
        .then((res) => setRefresh(!refresh))
        .catch((e) => console.log(e))
}

export function getSubscriptions(token, setData) {
    axios.get(`http://127.0.0.1:8000/api/get-subscriptions`, {headers: {'Authorization': "Bearer " + token.token.data.token}})
        .then((res) => setData(res.data))
        .catch((e) => console.log(e))
}