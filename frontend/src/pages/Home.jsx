import React from 'react'
import NavBar from '../components/NavBar.jsx'
import Hero from '../components/Hero.jsx'
import FeatureCards from '../components/FeatureCards.jsx'
import ProductShowcase from '../components/ProductShowcase.jsx'
import ExpertSection from '../components/ExpertSection.jsx'
import NewsSection from '../components/NewsSection.jsx'
import Footer from '../components/Footer.jsx'

export default function Home() {
  return (
    <div className="ds-app">
      <NavBar />
      <Hero />
      <FeatureCards />
      <ProductShowcase />
      <ExpertSection />
      <NewsSection />
      <Footer />
    </div>
  )
}
