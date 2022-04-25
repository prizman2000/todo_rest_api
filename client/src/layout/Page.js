import React, {useContext, useEffect, useState} from 'react';
import s from './Page.module.scss';
import {AppContext} from "../contexts/AppContext";
import { Layout, Menu, Breadcrumb, MenuProps } from 'antd';
import {getRole} from "../apiFunctions/auth";

import {
    DesktopOutlined,
    TeamOutlined,
    UserOutlined,
    LinkedinOutlined
} from '@ant-design/icons';
import AuthForm from "../components/AuthForm/AuthForm";
import MyPosts from "../components/MyPosts/MyPosts";
import Blogs from "../components/Blogs/Blogs";
import {getSubscriptions} from "../apiFunctions/blogs";
import Subscriptions from "../components/Subscriptions/Subscriptions";

const { Header, Content, Footer, Sider } = Layout;

function getItem(label, key, icon, children) {
    return {
        key,
        icon,
        children,
        label,
    };
}

export default function Page() {

    const [selectedNav, setSelectedNav] = useState(null);

    const {appToken, setUserRole} = useContext(AppContext);

    const [items, setItems] = useState([
        {
            label: 'Моя страница',
            key: '1',
            icon: <UserOutlined />
        },
        {
            label: 'Все блоги',
            key: '2',
            icon: <DesktopOutlined />
        },
        {
            label: 'Подписки',
            key: '3',
            icon: <TeamOutlined />,
        }
    ]);

    const handleSelectNav = (e) => {
        setSelectedNav(e.key);
    };


    useEffect(() => {
        if (appToken.success) {
            setSelectedNav("1");
            getRole(appToken, setUserRole);
        }
    }, [appToken]);

    return (
        <Layout style={{ minHeight: '100vh' }}>
            <Sider>
                <div className={s.logo}>
                    <LinkedinOutlined style={{color: '#ffffff', fontSize: 38}}/> Blogs
                </div>
                {items && items.length &&
                    <Menu
                        disabled={!appToken.success}
                        theme="dark"
                        selectedKeys={[`${selectedNav}`]}
                        onClick={(e) => handleSelectNav(e)}
                        mode="inline"
                        items={items}
                    />
                }
            </Sider>
            <Layout className="site-layout">
                <Header className="site-layout-background" style={{ padding: 0 }} />
                <Content style={{ margin: '0 16px' }}>
                    <Breadcrumb style={{ margin: '16px 0' }}>
                        {!appToken.success &&
                            <Breadcrumb.Item>Авторизация</Breadcrumb.Item>
                        }
                        {appToken.success &&
                            <>
                                {selectedNav === "1" ?
                                    <Breadcrumb.Item>Мои посты</Breadcrumb.Item>
                                : selectedNav === "2" ?
                                    <Breadcrumb.Item>Блоги</Breadcrumb.Item>
                                : <Breadcrumb.Item>Подписки</Breadcrumb.Item>}
                            </>
                        }
                    </Breadcrumb>
                    <div className="site-layout-background" style={{ padding: 24, minHeight: 360 }}>
                        {!appToken.success &&
                            <AuthForm/>
                        }
                        {appToken.success &&
                        <>
                            {selectedNav === "1" ?
                                <MyPosts />
                            : selectedNav === "2" ?
                                <Blogs />
                            : <Subscriptions/>}
                        </>
                        }
                    </div>
                </Content>
                <Footer style={{ textAlign: 'center' }}>In Blogs ©2022 Created by Lebedev V.R.</Footer>
            </Layout>
        </Layout>

    );
}