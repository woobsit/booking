import HeroSlider from '../components/HeroSlider'
import { Card, Row, Col } from 'antd'

export default function Home() {
  return (
    <main>
      <HeroSlider />
      
      <div style={{ padding: '60px 20px', maxWidth: '1200px', margin: '0 auto' }}>
        <Row gutter={[24, 24]}>
          <Col xs={24} sm={12} md={8}>
            <Card title="Express Services" variant="outlined">
              Non-stop routes with minimal travel time
            </Card>
          </Col>
          <Col xs={24} sm={12} md={8}>
            <Card title="Group Discounts" variant="outlined">
              Special rates for 5+ passengers
            </Card>
          </Col>
          <Col xs={24} sm={24} md={8}>
            <Card title="Safe Travel" variant="outlined">
              All buses sanitized and safety-certified
            </Card>
          </Col>
        </Row>
      </div>
    </main>
  )
}