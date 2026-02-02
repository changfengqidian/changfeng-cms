import React from 'react'

export default function ExpertSection() {
  const experts = Array.from({ length: 4 }).map((_, i) => ({
    id: i + 1,
    name: `专家姓名 ${i + 1}`,
    title: '首席技术官',
    avatar: `/assets/expert${i + 1}.png`
  }))

  return (
    <section className="expert-section">
      <div className="container">
        <h2>专家团队</h2>
        <div className="expert-grid">
          {experts.map(expert => (
            <div className="expert-card" key={expert.id}>
              <div className="expert-avatar">
                <img src={expert.avatar} alt={expert.name} />
              </div>
              <h3 className="expert-name">{expert.name}</h3>
              <p className="expert-title">{expert.title}</p>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}