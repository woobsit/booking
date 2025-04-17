import './globals.css'
import type { Metadata } from 'next'
import { Inter } from 'next/font/google'
//import AppFooter from '@/components/layout/Footer'

const inter = Inter({ subsets: ['latin'] })

export const metadata: Metadata = {
  title: 'Transport Booking System',
  description: 'Book your bus tickets online',
}

export default function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <html lang="en">
      <body className={inter.className}>
        {children}
        {/* <AppFooter /> */}
      </body>
    </html>
  )
}