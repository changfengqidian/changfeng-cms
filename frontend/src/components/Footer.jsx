import React from 'react'

export default function Footer() {
  const partners = Array.from({ length: 6 }).map((_, i) => `/assets/partner${i + 1}.png`)

  return (
    <footer className="ds-footer">
      <div className="container">
        <div className="footer-partners">
          <h3>合作伙伴</h3>
          <div className="partner-grid">
            {partners.map((src, i) => (
              <img key={i} src={src} alt={`合作伙伴 ${i + 1}`} className="partner-logo" />
            ))}
          </div>
        </div>
        <div className="footer-bottom">
          <p>&copy; 2026 设计系统示例. 保留所有权利.</p>
        </div>
      </div>
    </footer>
  )
}