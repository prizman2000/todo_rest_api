import React, {useContext} from "react";
import s from './AuthForm.module.scss';
import {Button, Form, Input} from "antd";
import {login} from "../../apiFunctions/auth";
import {AppContext} from "../../contexts/AppContext";

export default function AuthForm() {

    const {appToken, setAppToken} = useContext(AppContext);

    const onFinishFailed = (errorInfo) => {
        console.log('Failed:', errorInfo);
    };

    return (
        <>
            <div className={s.auth_page}>
                {appToken.message && <span className={s.err_msg}>Такого пользователя не существует!</span>}
                <Form
                    name="basic"
                    labelCol={{
                        span: 2,
                    }}
                    wrapperCol={{
                        span: 10,
                    }}
                    initialValues={{
                        remember: true,
                    }}
                    onFinish={(values) => login(values, setAppToken)}
                    onFinishFailed={onFinishFailed}
                    autoComplete="off"
                >
                    <Form.Item
                        label="Логин"
                        name="username"
                        rules={[
                            {
                                required: true,
                                message: 'Пожалуйста, введите логин!',
                            },
                        ]}
                    >
                        <Input />
                    </Form.Item>

                    <Form.Item
                        label="Пароль"
                        name="password"
                        rules={[
                            {
                                required: true,
                                message: 'Пожалуйста, введите пароль!',
                            }
                        ]}
                    >
                        <Input.Password />
                    </Form.Item>

                    <Form.Item
                        wrapperCol={{
                            offset: 2,
                            span: 16,
                        }}
                    >
                        <Button type="primary" htmlType="submit">
                            Войти
                        </Button>
                    </Form.Item>
                </Form>
            </div>
        </>
    );
}