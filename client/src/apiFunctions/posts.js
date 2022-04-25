import axios from "axios";

export function getAllPosts(token, setData) {
    axios.get('http://127.0.0.1:8000/api/post', {headers: {'Authorization': "Bearer " + token.token.data.token}})
        .then((res) => setData(res.data))
        .catch((e) => console.log(e))
}

export function addPost(post, token, refresh, setRefresh) {
    axios.post('http://127.0.0.1:8000/api/post', {...post}, {headers: {'Authorization': "Bearer " + token.token.data.token}})
        .then((res) => setRefresh(!refresh))
        .catch((e) => console.log(e))
}

export function deletePost(postId, token, refresh, setRefresh) {
    axios.delete(`http://127.0.0.1:8000/api/post/${postId}`,  {headers: {'Authorization': "Bearer " + token.token.data.token}})
        .then((res) => setRefresh(!refresh))
        .catch((e) => console.log(e))
}