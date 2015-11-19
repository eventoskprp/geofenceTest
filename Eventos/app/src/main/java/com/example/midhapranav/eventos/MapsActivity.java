package com.example.midhapranav.eventos;

import android.Manifest;
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.pm.PackageManager;
import android.graphics.Color;
import android.location.Address;
import android.location.Criteria;
import android.location.Geocoder;
import android.location.Location;
import android.location.LocationListener;
import android.location.LocationManager;
import android.support.v4.app.FragmentActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.google.android.gms.maps.CameraUpdate;
import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.SupportMapFragment;
import com.google.android.gms.maps.model.CircleOptions;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.Marker;
import com.google.android.gms.maps.model.MarkerOptions;

import java.util.List;

public class MapsActivity extends FragmentActivity implements LocationListener {

    private GoogleMap mMap; // Might be null if Google Play services APK is not available.

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_maps);
        setUpMapIfNeeded();
        setupCreateGeoFenceButton();
    }

    @Override
    protected void onResume() {
        super.onResume();
        setUpMapIfNeeded();
    }

    /**
     * Sets up the map if it is possible to do so (i.e., the Google Play services APK is correctly
     * installed) and the map has not already been instantiated.. This will ensure that we only ever
     * call {@link #setUpMap()} once when {@link #mMap} is not null.
     * <p>
     * If it isn't installed {@link SupportMapFragment} (and
     * {@link com.google.android.gms.maps.MapView MapView}) will show a prompt for the user to
     * install/update the Google Play services APK on their device.
     * <p>
     * A user can return to this FragmentActivity after following the prompt and correctly
     * installing/updating/enabling the Google Play services. Since the FragmentActivity may not
     * have been completely destroyed during this process (it is likely that it would only be
     * stopped or paused), {@link #onCreate(Bundle)} may not be called again so we should call this
     * method in {@link #onResume()} to guarantee that it will be called.
     */
    private void setUpMapIfNeeded() {
        // Do a null check to confirm that we have not already instantiated the map.
        if (mMap == null) {
            // Try to obtain the map from the SupportMapFragment.
            mMap = ((SupportMapFragment) getSupportFragmentManager().findFragmentById(R.id.map))
                    .getMap();
            mMap.setMyLocationEnabled(true);
            LocationManager locationManager = (LocationManager) getSystemService(LOCATION_SERVICE);
            Criteria criteria = new Criteria();
            String bestProvider = locationManager.getBestProvider(criteria, true);
            if (checkSelfPermission(Manifest.permission.ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED && checkSelfPermission(Manifest.permission.ACCESS_COARSE_LOCATION) != PackageManager.PERMISSION_GRANTED) {
                // TODO: Consider calling
                //    public void requestPermissions(@NonNull String[] permissions, int requestCode)
                // here to request the missing permissions, and then overriding
                //   public void onRequestPermissionsResult(int requestCode, String[] permissions,
                //                                          int[] grantResults)
                // to handle the case where the user grants the permission. See the documentation
                // for Activity#requestPermissions for more details.
                return;
            }
            Location location = locationManager.getLastKnownLocation(bestProvider);
            if (location != null) {
                onLocationChanged(location);
            }
            locationManager.requestLocationUpdates(bestProvider, 20000, 0, this);
            // Check if we were successful in obtaining the map.
            if (mMap != null) {
                setUpMap();
            }
        }
    }

    /**
     * This is where we can add markers or lines, add listeners or move the camera. In this case, we
     * just add a marker near Africa.
     * <p>
     * This should only be called once and when we are sure that {@link #mMap} is not null.
     */
    private void setUpMap() {
        //TODO setup-map with existing geoFences
    }

    private void setupCreateGeoFenceButton() {
        // Declare your builder here -
        Button mButton = (Button) findViewById(R.id.create_geo_fence_button);

        mButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                LinearLayout alertBoxLayout = new LinearLayout(MapsActivity.this);
                alertBoxLayout.setOrientation(LinearLayout.VERTICAL);

                AlertDialog.Builder alert = new AlertDialog.Builder(MapsActivity.this);
                alert.setTitle("Create GeoFence"); //Set Alert dialog title here
                // Set an EditText view to get user input
                final EditText address = new EditText(getApplicationContext());
                final TextView addressText = new TextView(getApplicationContext());
                final TextView radiusText = new TextView(getApplicationContext());
                final EditText radius = new EditText(getApplicationContext());
                final EditText name = new EditText(getApplicationContext());
                final TextView nameText = new TextView(getApplicationContext());

                addressText.setText("\t Enter Address here:");
                addressText.setTextColor(Color.BLACK);
                alertBoxLayout.addView(addressText);
                alertBoxLayout.addView(address);
                radiusText.setText("\t Enter Radius here:");
                nameText.setTextColor(Color.BLACK);
                alertBoxLayout.addView(radiusText);
                alertBoxLayout.addView(radius);
                nameText.setText("\t Enter Name here:");
                radiusText.setTextColor(Color.BLACK);
                alertBoxLayout.addView(nameText);
                alertBoxLayout.addView(name);

                alert.setView(alertBoxLayout);
                alert.setPositiveButton("OK", new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int whichButton) {
                        //You will get as string input data in this variable.
                        // here we convert the input to a string and show in a toast.
                        String srt = address.getEditableText().toString();
                        setLocationFromStringAddress(address.getEditableText().toString(), Double.parseDouble(radius.getEditableText().toString()),name.getEditableText().toString());
                    }
                });
                alert.setNegativeButton("CANCEL", new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int whichButton) {
                        // Canceled.
                        dialog.cancel();
                    }
                });
                AlertDialog alertDialog = alert.create();
                alertDialog.show();
            }
        });
    }

    public void setLocationFromStringAddress(String strAddress, double radius, String name){

        Geocoder coder = new Geocoder(getApplicationContext());
        List<Address> address;

        try {
            address = coder.getFromLocationName(strAddress,5);
            if (address == null) {
                return;
            }
            Address location = address.get(0);
            location.getLatitude();
            location.getLongitude();
            LatLng coordinate = new LatLng(location.getLatitude(), location.getLongitude());
            double radiusInMeters = radius;
            int strokeColor = 0xffff0000; //red outline
            int shadeColor = 0x44ff0000; //opaque red fill
            CircleOptions circleOptions = new CircleOptions().center(coordinate).radius(radiusInMeters).fillColor(shadeColor).strokeColor(strokeColor).strokeWidth(8);
            mMap.addCircle(circleOptions);
            Marker m = mMap.addMarker(new MarkerOptions().position(coordinate).title(name));
            CameraUpdate yourLocation = CameraUpdateFactory.newLatLngZoom(coordinate, 11.0f);
            mMap.animateCamera(yourLocation);
            return;
        }
        catch (Exception e){}
    }


    @Override
    public void onLocationChanged(Location location) {
        double latitude = location.getLatitude();
        double longitude = location.getLongitude();
        LatLng latLng = new LatLng(latitude, longitude);
        mMap.addMarker(new MarkerOptions().position(latLng));
        mMap.moveCamera(CameraUpdateFactory.newLatLng(latLng));
        mMap.animateCamera(CameraUpdateFactory.zoomTo(15));
        Log.d("Map debug->",latitude +","+longitude);
    }

    @Override
    public void onStatusChanged(String provider, int status, Bundle extras) {

    }

    @Override
    public void onProviderEnabled(String provider) {

    }

    @Override
    public void onProviderDisabled(String provider) {

    }
}
