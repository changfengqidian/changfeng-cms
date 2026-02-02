import React from 'react'

export default function ProductShowcase() {
  const products = Array.from({ length: 6 }).map((_, i) => ({
    id: i + 1,
    name: `产品系列 ${i + 1}`,
    desc: '高性能解决方案，助力企业数字化转型',
    image: `/assets/product${i + 1}.png`
  }))

  return (
    <section className="product-showcase">
      <div className="container">
        <h2>产品展示</h2>
        <div className="card-grid">
          {products.map(item => (
            <div className="card product-card" key={item.id}>
              <div className="card-media">
                <img src={item.image} alt={item.name} />
              </div>
              <div className="card-body">
                <h3 className="card-title">{item.name}</h3>
                <p className="card-desc">{item.desc}</p>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}