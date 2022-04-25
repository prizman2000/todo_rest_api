import React, {useContext, useEffect, useState} from "react";
import {addPost, deletePost, getAllPosts} from "../../apiFunctions/posts";
import {AppContext} from "../../contexts/AppContext";
import s from './MyPosts.module.scss';

import {Button, Collapse, Input, Modal, Radio, Space} from 'antd';
import {LoadingOutlined} from "@ant-design/icons";
import {addPostInGroup, getAllBlogs} from "../../apiFunctions/blogs";

const { Panel } = Collapse;
const { TextArea } = Input;

export default function MyPosts() {

    const {appToken, userRole} = useContext(AppContext);

    const [newPost, setNewPost] = useState({title: '', description: ''});
    const [posts, setPosts] = useState([]);
    const [addPostModal, setAddPostModal] = useState(false);
    const [addPostModalAdd, setAddPostModalAdd] = useState(false);
    const [addedPostInGroup, setAddedPostInGroup] = useState({group_id: 1, post_id: 1});
    const [blogs, setBlogs] = useState([]);
    const [refresh, setRefresh] = useState(false);

    const showModal = () => {
        setAddPostModal(true);
    };

    const showModalAdd = (id) => {
        setAddedPostInGroup({...addedPostInGroup, post_id: id})
        setAddPostModalAdd(true);
    };

    const handleCancel = () => {
        setAddPostModal(false);
    };

    const handleCancelAdd = () => {
        setAddPostModalAdd(false);
    };

    const handleAddPost = () => {
        setAddPostModal(false);
        addPost(newPost, appToken, refresh, setRefresh);
    };

    const handleAddPostAdd = () => {
        setAddPostModalAdd(false);
        addPostInGroup(addedPostInGroup, appToken, refresh, setRefresh);
    };

    const handleDeletePost = (postId) => {
        deletePost(postId, appToken, refresh, setRefresh);
    };

    useEffect(() => {
        setTimeout(() => {
            getAllPosts(appToken,setPosts);
        }, 500);
        getAllBlogs(appToken, setBlogs);
    }, [refresh]);

    return (
        <>
            {posts.length === 0 ?
                <LoadingOutlined style={{marginLeft: 20, fontSize: 38}}/>
            :
                <div className={s.content}>
                    <Collapse accordion>
                        {posts.map((item, i) =>
                            <Panel
                                header={item.title}
                                key={i}
                                extra={[
                                    userRole.data.role === 'admin' &&
                                    <Button className={s.add_in_blog} type={'primary'} onClick={() => showModalAdd(item.id)}>Добавить в блог</Button>,
                                    <Button danger onClick={() => handleDeletePost(item.id)}>Удалить</Button>
                                ]}
                            >
                                <p>{item.description}</p>
                            </Panel>
                        )}
                    </Collapse>
                    <Button type="primary" className={s.add_btn} onClick={showModal}>
                        Новый пост
                    </Button>
                    <Modal
                        title="Новый пост"
                        visible={addPostModal}
                        onOk={() => handleAddPost()}
                        onCancel={handleCancel}
                        footer={[
                            <Button key="back" onClick={handleCancel}>
                                Отменить
                            </Button>,
                            <Button key="submit" type="primary" onClick={handleAddPost}>
                                Добавить
                            </Button>,
                        ]}
                    >
                        <div className={s.input_block}>
                            <Input
                                value={newPost.title}
                                onChange={(e) => setNewPost({...newPost, title: e.target.value})}
                                placeholder={'Введите заголовок...'}
                            />
                            <TextArea
                                value={newPost.description}
                                onChange={(e) => setNewPost({...newPost, description: e.target.value})}
                                placeholder={'Введите текст поста...'}
                                autoSize={{ minRows: 3, maxRows: 5 }}
                            />
                        </div>
                    </Modal>
                    <Modal
                        title="Добавить пост в группу"
                        visible={addPostModalAdd}
                        onOk={() => handleAddPostAdd()}
                        onCancel={handleCancelAdd}
                        footer={[
                            <Button key="back" onClick={handleCancelAdd}>
                                Отменить
                            </Button>,
                            <Button key="submit" type="primary" onClick={handleAddPostAdd}>
                                Добавить
                            </Button>,
                        ]}
                    >
                        <Radio.Group onChange={(e) => setAddedPostInGroup({...addedPostInGroup, group_id: e.target.value})} value={addedPostInGroup.group_id}>
                            <Space direction="vertical">
                                {blogs.map((item, i) =>
                                    <>
                                        {item.owner &&
                                            <Radio key={i} value={item.id}>{item.name}</Radio>
                                        }
                                    </>
                                )}
                            </Space>
                        </Radio.Group>
                    </Modal>
                </div>
            }
        </>
    );
}