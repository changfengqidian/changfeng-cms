import React from 'react'

export default function NewsSection() {
  const news = Array.from({ length: 3 }).map((_, i) => ({
    id: i + 1,
    title: `最新动态 ${i + 1}`,
    desc: '公司发布新产品，引领行业技术创新',
    image: `/assets/news${i + 1}.png`,
    date: '2026-01-26'
  }))

  return (
    <section className="news-section">
      <div className="container">
        <h2>新闻动态</h2>
        <div className="news-grid">
          {news.map(item => (
            <article className="news-card" key={item.id}>
              <div className="news-image">
                <img src={item.image} alt={item.title} />
              </div>
              <div className="news-content">
                <h3 className="news-title">{item.title}</h3>
                <p className="news-desc">{item.desc}</p>
                <time className="news-date">{item.date}</time>
              </div>
            </article>
          ))}
        </div>
      </div>
    </section>
  )
}