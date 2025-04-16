import React, { useState, useEffect } from "react";
import { Head, usePage, useForm } from "@inertiajs/react";
import axios from "axios";

export default function Index() {
    const { notes } = usePage().props;
    const [currentUser, setCurrentUser] = useState(null);
    const [prevNotesLength, setPrevNotesLength] = useState(notes.length);
    const [elapsedTime, setElapsedTime] = useState(0);
    const [sessionLifetimeInSeconds, setSessionLifetimeInSeconds] = useState(0);

    const { data, setData, post, processing, errors, reset } = useForm({
        title: "",
        description: "",
        images: "",
    });

    // Fetch current user data on initial load and when notes length changes
    useEffect(() => {
        const fetchUserData = () => {
            axios
                .get("/api/user/show")
                .then((response) => {
                    if (response.data && response.data.data) {
                        const userData = response.data.data;
                        setCurrentUser(userData);
                        setSessionLifetimeInSeconds(userData.lifetime);
                        setPrevNotesLength(notes.length);

                        // Initialize elapsed time immediately
                        const lastActivity = new Date(
                            userData.last_activity * 1000
                        );
                        const now = new Date();
                        const diffInSeconds = Math.floor(
                            (now - lastActivity) / 1000
                        );
                        const remainingTime = userData.lifetime - diffInSeconds;
                        setElapsedTime(Math.max(0, remainingTime));
                    }
                })
                .catch((err) =>
                    console.error("Error fetching user session:", err)
                );
        };

        // Fetch immediately on mount
        fetchUserData();

        // Also fetch when notes length changes
        if (notes.length !== prevNotesLength) {
            fetchUserData();
        }
    }, [notes.length, prevNotesLength]);

    // Update elapsed time countdown
    useEffect(() => {
        if (!currentUser?.last_activity || !sessionLifetimeInSeconds) return;

        const timer = setInterval(() => {
            setElapsedTime((prev) => {
                const newTime = prev - 1;
                return newTime >= 0 ? newTime : 0;
            });
        }, 1000);

        return () => clearInterval(timer);
    }, [currentUser, sessionLifetimeInSeconds]);

    // Format seconds as HH:MM:SS
    const formatElapsedTime = (seconds) => {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;

        return [
            hours.toString().padStart(2, "0"),
            minutes.toString().padStart(2, "0"),
            secs.toString().padStart(2, "0"),
        ].join(":");
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post("/add/notes", {
            onSuccess: () => {
                reset();
                setData({
                    title: "",
                    description: "",
                    images: "",
                });
            },
        });
    };

    return (
        <>
            <Head title="Notes" />
            <div className="max-w-4xl mx-auto py-8 px-4">
                {currentUser && (
                    <div className="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded">
                        <div className="flex justify-between items-center">
                            <div>
                                <h3 className="font-bold text-lg">
                                    {currentUser.isGuest
                                        ? "Guest User"
                                        : "Registered User"}
                                    : {currentUser.name || currentUser.username}
                                </h3>
                                <p className="text-gray-600">
                                    Username: {currentUser.username}
                                </p>
                                <p className="text-gray-600">
                                    Session expires in:{" "}
                                    <span className="font-medium">
                                        {formatElapsedTime(elapsedTime)}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                )}

                <h1 className="text-2xl font-bold mb-6">Notes Page</h1>

                {/* Add Note Form */}
                <div className="bg-white shadow rounded-lg p-6 mb-8">
                    <h2 className="text-xl font-semibold mb-4">Add New Note</h2>
                    <form onSubmit={handleSubmit}>
                        <div className="mb-4">
                            <label
                                className="block text-gray-700 text-sm font-bold mb-2"
                                htmlFor="title"
                            >
                                Title
                            </label>
                            <input
                                type="text"
                                id="title"
                                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value={data.title}
                                onChange={(e) =>
                                    setData("title", e.target.value)
                                }
                                required
                            />
                            {errors.title && (
                                <div className="text-red-600 text-sm mt-1">
                                    {errors.title}
                                </div>
                            )}
                        </div>

                        <div className="mb-4">
                            <label
                                className="block text-gray-700 text-sm font-bold mb-2"
                                htmlFor="description"
                            >
                                Description
                            </label>
                            <textarea
                                id="description"
                                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value={data.description}
                                onChange={(e) =>
                                    setData("description", e.target.value)
                                }
                                rows="4"
                            />
                            {errors.description && (
                                <div className="text-red-600 text-sm mt-1">
                                    {errors.description}
                                </div>
                            )}
                        </div>

                        <div className="mb-6">
                            <label
                                className="block text-gray-700 text-sm font-bold mb-2"
                                htmlFor="images"
                            >
                                Image URL
                            </label>
                            <input
                                type="text"
                                id="images"
                                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value={data.images}
                                onChange={(e) =>
                                    setData("images", e.target.value)
                                }
                            />
                            {errors.images && (
                                <div className="text-red-600 text-sm mt-1">
                                    {errors.images}
                                </div>
                            )}
                        </div>

                        <div className="flex items-center justify-end">
                            <button
                                type="submit"
                                className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                disabled={processing}
                            >
                                {processing ? "Adding..." : "Add Note"}
                            </button>
                        </div>
                    </form>
                </div>

                {/* Notes List */}
                <div className="bg-white shadow rounded-lg p-6">
                    <h2 className="text-xl font-semibold mb-4">Your Notes</h2>
                    {notes && notes.length > 0 ? (
                        <ul className="divide-y divide-gray-200">
                            {notes.map((note, index) => (
                                <li key={index} className="py-4">
                                    <h3 className="text-lg font-medium">
                                        {note.title}
                                    </h3>
                                    {note.description && (
                                        <p className="text-gray-600 mt-1">
                                            {note.description}
                                        </p>
                                    )}
                                    {note.images && (
                                        <div className="mt-2">
                                            <img
                                                src={note.images}
                                                alt={note.title}
                                                className="w-32 h-32 object-cover rounded"
                                            />
                                        </div>
                                    )}
                                </li>
                            ))}
                        </ul>
                    ) : (
                        <p className="text-gray-500">
                            No notes found. Add your first note above!
                        </p>
                    )}
                </div>
            </div>
        </>
    );
}
