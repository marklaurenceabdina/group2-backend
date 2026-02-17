import './bootstrap'
import React from 'react'
import ReactDOM from 'react-dom/client'
import { BrowserRouter, Routes, Route } from 'react-router-dom'

import NavBar from './pages/NavBar'
import Home from './pages/sections/Home'

function App() {
    return (
        <BrowserRouter>
            <div className="min-h-screen bg-gray-100">

                {/* Navigation */}
                <NavBar />

                {/* Page Content */}
                <Routes>
                    <Route path="/" element={<Home />} />
                </Routes>

            </div>
        </BrowserRouter>
    )
}

ReactDOM.createRoot(document.getElementById('app')).render(<App />)