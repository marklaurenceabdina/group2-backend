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
                            Brgy. San Vicente, Sta. Elena<br />
                            Camarines Norte, Philippines 4612
                        </p>

                        <p className="text-lg text-gray-600 mb-6">
                            <span className="font-semibold">📞 Contact:</span><br />
                            +63 (123) 456-7890<br />
                            hello@waterlandresort.com
                        </p>

                        <p className="text-lg text-gray-600">
                            <span className="font-semibold">🚗 From Manila:</span><br />
                            4-hour drive via North Luzon Expressway
                        </p>
                    </div>

                    <div className="h-96 rounded-2xl shadow-2xl bg-linear-to-br from-sky-200 to-sky-400 flex items-center justify-center text-white text-lg font-semibold">
                        Google Maps Location
                    </div>

                </div>
            </div>
        </section>
    )
}
