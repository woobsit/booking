'use client'
import { Carousel, Button, Typography } from 'antd'
import AppHeader from './Header'
import React from 'react' // Make sure React is imported

const { Title, Paragraph } = Typography

const slides = [
  {
    id: 1,
    title: 'Nairobi to Mombasa',
    description: 'Daily departures • 8hr journey • From KES 2,500',
    image: 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1600&q=80',
    cta: 'Book Now',
    ctaLink: '/booking'
  },
  {
    id: 2,
    title: 'Luxury Executive Class',
    description: 'Premium comfort • Extra legroom • Free snacks',
    image: 'https://images.unsplash.com/photo-1502877338535-766e1452684a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1600&q=80',
    cta: 'View Seats',
    ctaLink: '/seats'
  }
]

export default function HeroSlider() {
  return (
    <div style={{ position: 'relative', width: '100%', overflow: 'hidden' }}>
      <AppHeader />
      
      <Carousel 
        autoplay 
        effect="fade" 
        dotPosition="bottom"
        style={{ width: '100%', height: '100vh' }}
      >
        {slides.map((slide) => (
          <div key={slide.id}>
            <div style={{
              height: '100vh',
              minHeight: '600px',
              background: `linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url(${slide.image})`,
              backgroundSize: 'cover',
              backgroundPosition: 'center',
              display: 'flex',
              flexDirection: 'column',
              justifyContent: 'center',
              alignItems: 'center',
              textAlign: 'center',
              color: 'white'
            }}>
              <div style={{ 
                maxWidth: '800px',
                padding: '0 20px',
                marginTop: '60px' // Adjusted for header
              }}>
                <Title level={1} style={{ 
                  color: 'white',
                  fontSize: 'clamp(2rem, 5vw, 3.5rem)',
                  marginBottom: '24px',
                  textShadow: '2px 2px 4px rgba(0,0,0,0.5)'
                }}>
                  {slide.title}
                </Title>
                <Paragraph style={{ 
                  fontSize: 'clamp(1rem, 2vw, 1.25rem)',
                  marginBottom: '40px',
                  textShadow: '1px 1px 2px rgba(0,0,0,0.5)'
                }}>
                  {slide.description}
                </Paragraph>
                <Button 
                  type="primary" 
                  size="large"
                  href={slide.ctaLink}
                  style={{ 
                    padding: '0 40px',
                    height: '50px',
                    fontSize: '18px'
                  }}
                >
                  {slide.cta}
                </Button>
              </div>
            </div>
          </div>
        ))}
      </Carousel>
    </div>
  )
}