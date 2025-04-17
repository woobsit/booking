'use client'
import { MenuOutlined } from '@ant-design/icons'
import { Menu, Button, Row, Col } from 'antd'
import type { MenuProps } from 'antd'
import { useState } from 'react'
import Link from 'next/link'
import Image from 'next/image'
import logo from './../assets/images/logo.png' // Import the logo


type MenuItem = Required<MenuProps>['items'][number]

const items: MenuItem[] = [
  {
    label: <Link href="/">Home</Link>,
    key: 'home',
  },
  {
    label: <Link href="/about-us">About us</Link>,
    key: 'about',
  },
  {
    label: <Link href="/our-services">Our services</Link>,
    key: 'services',
  },
  {
    label: 'Routes',
    key: 'routes',
    children: [
      { label: <Link href="/routes/nairobi-mombasa">Nairobi to Mombasa</Link>, key: 'route:1' },
      { label: <Link href="/routes/nairobi-kisumu">Nairobi to Kisumu</Link>, key: 'route:2' },
    ],
  },
  {
    label: <Link href="/contact">Contact</Link>,
    key: 'contact',
  },

  {
    label: <Link href="/account">Account</Link>,
    key: 'account',
    children: [
      { label: <Link href="/register">Register</Link>, key: 'register' },
      { label: <Link href="/login">Login</Link>, key: 'login' },
    ],
  },
]

export default function AppHeader() {
  const [current, setCurrent] = useState('home')
  const [mobileMenuVisible, setMobileMenuVisible] = useState(false)

  const onClick: MenuProps['onClick'] = (e) => {
    setCurrent(e.key)
    setMobileMenuVisible(false)
  }

  return (
    <header style={{
      position: 'absolute',
      top: 0,
      left: 0,
      right: 0,
      zIndex: 100,
      background: 'transparent',
      padding: '20px 40px',
    }}>
      <Row justify="space-between" align="middle">
        {/* Logo */}
        <Col>
          <Link href="/">
          <Image
            src={logo}
            alt="Transport Logo"
            height={60}
            priority
  />
          </Link>
        </Col>

        {/* Desktop Menu */}
        <Col xs={0} sm={0} md={24}>
          <Menu
            onClick={onClick}
            selectedKeys={[current]}
            mode="horizontal"
            items={items}
            style={{
              background: 'transparent',
              color: 'white',
              borderBottom: 'none',
              justifyContent: 'flex-end',
              fontSize: '16px',
            }}
            theme="dark"
          />
        </Col>

        {/* Mobile Menu Button */}
        <Col xs={24} sm={24} md={0}>
          <Button
            icon={<MenuOutlined style={{ color: 'white' }} />}
            onClick={() => setMobileMenuVisible(!mobileMenuVisible)}
            type="text"
            size="large"
          />
        </Col>
      </Row>

      {/* Mobile Menu Dropdown */}
      {mobileMenuVisible && (
        <div style={{ background: 'rgba(0,0,0,0.8)', marginTop: 20 }}>
          <Menu
            onClick={onClick}
            selectedKeys={[current]}
            mode="vertical"
            items={items}
            style={{ background: 'transparent' }}
            theme="dark"
          />
        </div>
      )}
    </header>
  )
}