import React, {useContext, useEffect, useState} from "react";
import {AppContext} from "../../contexts/AppContext";
import s from './Subscriptions.module.scss';
import {getPostsInGroup, getSubscriptions} from "../../apiFunctions/blogs";
import {LoadingOutlined} from "@ant-design/icons";
import {Button, Collapse} from "antd";

const { Panel } = Collapse;

export default function Subscriptions() {

    const {appToken, setUserRole} = useContext(AppContext);

    const [subscriptions, setSubscriptions] = useState([]);
    const [blogPosts, setBlogPosts] = useState([]);

    const loadPostsInBlog = (id) => {
        setBlogPosts([]);
        getPostsInGroup(id, appToken, setBlogPosts);
    };

    useEffect(() => {
        setTimeout(() => {
            getSubscriptions(appToken, setSubscriptions);
        }, 500)
    }, []);

    console.log(subscriptions)

    return (
        <>
            {subscriptions.length === 0 ?
                <LoadingOutlined style={{marginLeft: 20, fontSize: 38}}/>
            :
                <Collapse accordion onChange={(e) => loadPostsInBlog(subscriptions[e].id)}>
                    {subscriptions.map((item, i) =>
                        <Panel
                            header={item.name}
                            key={i}
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
                    )}
                </Collapse>
            }
        </>
    );
}