export default function Location() {
    return (
        <section className="py-28 px-6 md:px-24 bg-sky-50" id="location">
            <div className="max-w-6xl mx-auto">
                <div className="grid md:grid-cols-2 gap-16 items-center">

                    <div>
                        <h2 className="text-5xl font-bold mb-8 text-sky-800">
                            Find Us Here
                        </h2>

                        <p className="text-lg text-gray-600 mb-6">
                            <span className="font-semibold">📍 Address:</span><br />
                            Brgy. Osias, Kabacan<br />
                            North Cotabato, Philippines 9407
                        </p>

                        <p className="text-lg text-gray-600 mb-6">
                            <span className="font-semibold">📞 Contact:</span><br />
                            +63 (123) 456-7890<br />
                            hello@waterlandresort.com
                        </p>

                        <p className="text-lg text-gray-600">
                            <span className="font-semibold">🚗 From 7/11 Shell:</span><br />
                            2-minutes drive via Bonifacio - Osias Road
                        </p>
                    </div>

                    <div className="h-96 rounded-2xl shadow-2xl overflow-hidden">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1979.6026808351057!2d124.8339952453707!3d7.102182060453228!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x32f88d113a5cc367%3A0x51ce6a7ec700470!2sWaterland%20Resort!5e0!3m2!1sen!2sph!4v1771947746813!5m2!1sen!2sph"
                            title="Waterland Resort location"
                            className="w-full h-full"
                            style={{ border: 0 }}
                            allowFullScreen=""
                            loading="lazy"
                            referrerPolicy="no-referrer-when-downgrade"
                        />
                    </div>

                </div>
            </div>
        </section>
    )
}
