import React from 'react'

export default function FeatureCards() {
  const features = [
    { icon: '/assets/feature1.png', title: 'AI 智能分析', desc: '基于深度学习的智能分析平台' },
    { icon: '/assets/feature2.png', title: '数据可视化', desc: '实时数据展示与交互式图表' },
    { icon: '/assets/feature3.png', title: '云端协作', desc: '多用户实时协作与版本管理' },
    { icon: '/assets/feature4.png', title: '安全防护', desc: '企业级安全与数据加密' }
  ]

  return (
    <section className="feature-cards">
      <div className="container">
        <h2>核心功能</h2>
        <div className="card-grid">
          {features.map((item, i) => (
            <div className="card" key={i}>
              <div className="card-icon">
                <img src={item.icon} alt={item.title} />
              </div>
              <h3 className="card-title">{item.title}</h3>
              <p className="card-desc">{item.desc}</p>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}