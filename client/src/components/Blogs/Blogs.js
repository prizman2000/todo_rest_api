import React, {useContext, useEffect, useState} from "react";
import {addPost, deletePost, getAllPosts} from "../../apiFunctions/posts";
import {AppContext} from "../../contexts/AppContext";
import s from './Blogs.module.scss';

import {Button, Collapse, Input, Modal} from 'antd';
import {LoadingOutlined} from "@ant-design/icons";
import {addGroup, getAllBlogs, getPostsInGroup, subscribe} from "../../apiFunctions/blogs";

const { Panel } = Collapse;
const { TextArea } = Input;

export default function Blogs() {

    const {appToken, userRole} = useContext(AppContext);

    const [newBlog, setNewBlog] = useState({name: ''});
    const [blogs, setBlogs] = useState([]);
    const [addBlogModal, setAddBlogModal] = useState(false);
    const [refresh, setRefresh] = useState(false);

    const [blogPosts, setBlogPosts] = useState([]);

    const showModal = () => {
        setAddBlogModal(true);
    };

    const handleCancel = () => {
        setAddBlogModal(false);
    };

    const handleAddBlog = () => {
        setAddBlogModal(false);
        addGroup(newBlog, appToken, refresh, setRefresh);
    };

    const handleDeleteBlog = (blogId) => {
        deletePost(blogId, appToken, refresh, setRefresh);
    };

    const loadPostsInBlog = (id) => {
        setBlogPosts([]);
        getPostsInGroup(id, appToken, setBlogPosts);
    };

    const handleSubscribe = (id) => {
        subscribe({group_id: id}, appToken, refresh, setRefresh)
    };

    useEffect(() => {
        setTimeout(() => {
            getAllBlogs(appToken, setBlogs);
        }, 500)
    }, [refresh]);

    return (
        <>
            {blogs.length === 0 ?
                <LoadingOutlined style={{marginLeft: 20, fontSize: 38}}/>
            :
                <div className={s.content}>
                    {userRole.data.role === 'admin' &&
                        <div className={s.my_blogs}>
                            <span className={s.title}>Мои блоги</span>
                            <Collapse accordion onChange={(e) => loadPostsInBlog(blogs[e].id)}>
                                {blogs.map((item, i) =>
                                    <>
                                        {item.owner &&
                                        <Panel
                                            header={item.name}
                                            key={i}
                                            extra={[
                                                <Button danger onClick={() => handleDeleteBlog(item.id)}>Удалить</Button>
                                            ]}
                                        >
                                            {blogPosts.length === 0 ?
                                                <LoadingOutlined style={{marginLeft: 20, fontSize: 34}}/>
                                            :
                                                <Collapse accordion>
                                                    {blogPosts.map((item, i) =>
                                                        <Panel
                                                            header={item.title}
                                                            key={i}
                                                        >
                                                            <p>{item.description}</p>
                                                        </Panel>
                                                    )}
                                                </Collapse>
                                            }
                                        </Panel>
                                        }
                                    </>
                                )}
                            </Collapse>
                            <Button type="primary" className={s.add_btn} onClick={showModal}>
                                Новый блог
                            </Button>
                        </div>

                    }
                    <Collapse accordion onChange={(e) => loadPostsInBlog(blogs[e].id)}>
                        {blogs.map((item, i) =>
                            <>
                                {!item.owner &&
                                    <Panel
                                        header={item.name}
                                        key={i}
                                        extra={[
                                            <Button className={s.add_in_blog} type={'primary'} onClick={() => handleSubscribe(item.id)}>Подписаться</Button>
                                        ]}
                                    >
                                        {blogPosts.length === 0 ?
                                            <LoadingOutlined style={{marginLeft: 20, fontSize: 34}}/>
                                            :
                                            <Collapse accordion>
                                                {blogPosts.map((item, i) =>
                                                    <Panel
                                                        header={item.title}
                                                        key={i}
                                                    >
                                                        <p>{item.description}</p>
                                                    </Panel>
                                                )}
                                            </Collapse>
                                        }
                                    </Panel>
                                }
                            </>
                        )}
                    </Collapse>
                    {userRole.data.role === 'admin' &&
                        <>
                            <Modal
                                title="Новый блог"
                                visible={addBlogModal}
                                onOk={() => handleAddBlog()}
                                onCancel={handleCancel}
                                footer={[
                                    <Button key="back" onClick={handleCancel}>
                                        Отменить
                                    </Button>,
                                    <Button key="submit" type="primary" onClick={handleAddBlog}>
                                        Добавить
                                    </Button>,
                                ]}
                            >
                                <div className={s.input_block}>
                                    <Input
                                        value={newBlog.name}
                                        onChange={(e) => setNewBlog({name: e.target.value})}
                                        placeholder={'Введите название блога...'}
                                    />
                                </div>
                            </Modal>
                        </>
                    }
                </div>
            }
        </>
    );
}