<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Appointment;
use Illuminate\Http\Request;

class Booking extends Controller
{
    public function bookAppointment(Request $request)
    {
        // Validate the input
        $request->validate([
            'name' => 'required|string|max:255',
            'service' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|min:11',
            'date' => 'required|date|max:255',
            'time' => 'required',
            'notes' => 'nullable',
        ]);

        // Create a new supplier in the database
        $booking = Appointment::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Your appointment has been made successfully! We will contact you soon.',
            'data' => $booking,
        ], 201);
    }

    public function listAppointments()
    {
        $bookings = Appointment::all();
        return response()->json([
            'success' => true,
            'data' => $bookings,
        ]);
    }

    public function updateAppointment(Request $request, $id)
    {
        try {
            // Validate the input
            $request->validate([
                'name' => 'required|string|max:255',
                'service' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|min:11',
                'date' => 'required|date|max:255',
                'time' => 'required',
                'notes' => 'nullable',
                'status' => 'required|in:Confirmed,Completed,Cancelled,Pending',
            ]);

            // Update the appointment in the database
            $booking = Appointment::findOrFail($id);
            $booking->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Your appointment has been updated successfully!',
                'data' => $booking,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(), // You can remove this in production
            ], 500);
        }
    }


    public function deleteAppointment($id)
    {
        // Delete the appointment from the database
        $booking = Appointment::findOrFail($id);
        $booking->delete();
        return response()->json([
            'success' => true,
            'message' => 'Your appointment has been deleted successfully!',
        ]);
    }

    public function showAppointment($id)
    {
        // Fetch the appointment from the database
        $booking = Appointment::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $booking,
        ]);
    }
}
