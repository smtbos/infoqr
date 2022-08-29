package com.smtbos.infoqr;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;

import android.Manifest;
import android.app.Activity;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.pm.PackageManager;
import android.location.Criteria;
import android.location.Location;
import android.location.LocationManager;
import android.net.Uri;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.provider.Settings;
import android.util.Log;
import android.view.View;
import android.widget.Toast;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.Volley;
import com.budiyev.android.codescanner.CodeScanner;
import com.budiyev.android.codescanner.CodeScannerView;
import com.budiyev.android.codescanner.DecodeCallback;
import com.google.zxing.Result;

import org.json.JSONObject;

import java.util.ArrayList;

public class ScanQrActivity extends AppCompatActivity {

    private final int REQUEST_CODE = 3;
    private final String TAG = "TIMS";

    private SharedPreferences sharedPreferences;
    private SharedPreferences.Editor editor;

    private RequestQueue requestQueue;

    private LocationManager locationManager;

    private CodeScanner codeScanner;

    private int u_id = 0;
    private String latitude = "", longitude = "";

    private CodeScannerView codeScannerView;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_scan_qr);

        sharedPreferences = getSharedPreferences("InfoQR", MODE_PRIVATE);
        editor = sharedPreferences.edit();

        requestQueue = Volley.newRequestQueue(this);

        locationManager = (LocationManager) getSystemService(Context.LOCATION_SERVICE);

        verifyLogin();

        ArrayList<String> requestPermissionsList = new ArrayList<String>();

        // Camera Permission
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.CAMERA) != PackageManager.PERMISSION_GRANTED) {
            // ActivityCompat.requestPermissions(this, new String[]{Manifest.permission.CAMERA}, getResources().getInteger(R.integer.REQUEST_CODE_CAMERA));
            requestPermissionsList.add(Manifest.permission.CAMERA);
        }
        // Internet Permission
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.INTERNET) != PackageManager.PERMISSION_GRANTED) {
            // ActivityCompat.requestPermissions(this, new String[]{Manifest.permission.INTERNET}, getResources().getInteger(R.integer.REQUEST_CODE_INTERNET));
            requestPermissionsList.add(Manifest.permission.INTERNET);
        }
        // Location Permission
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED) {
            // ActivityCompat.requestPermissions(this, new String[]{Manifest.permission.ACCESS_FINE_LOCATION}, getResources().getInteger(R.integer.REQUEST_CODE_LOCATION));
            requestPermissionsList.add(Manifest.permission.ACCESS_FINE_LOCATION);
        }
        if (requestPermissionsList.size() > 0) {
            Log.d(TAG, "onCreate: " + requestPermissionsList.toString());
            String[] requestPermissionsArray = new String[requestPermissionsList.size()];
            for (int i = 0; i < requestPermissionsList.size(); i++) {
                requestPermissionsArray[i] = String.valueOf(requestPermissionsList.get(i));
            }
            ActivityCompat.requestPermissions(this, requestPermissionsArray, REQUEST_CODE);
        }
        // Check GPS
        if (!locationManager.isProviderEnabled(LocationManager.GPS_PROVIDER)) {
            final AlertDialog.Builder builder = new AlertDialog.Builder(this);

            builder.setMessage("Please Turn on GPS");
            builder.setCancelable(false);
            builder.setPositiveButton("Yes", new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialogInterface, int i) {
                    startActivity(new Intent(Settings.ACTION_LOCATION_SOURCE_SETTINGS));
                }
            });
            builder.setNegativeButton("No", new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialogInterface, int i) {
                    dialogInterface.cancel();
                    // Add Handling Code Here
                }
            });
            final AlertDialog alertDialog = builder.create();
            alertDialog.show();
        }
        getLocation();
        codeScannerView = (CodeScannerView) findViewById(R.id.scanner);
        codeScanner = new CodeScanner(this, codeScannerView);
        codeScanner.setDecodeCallback(new DecodeCallback() {
            @Override
            public void onDecoded(@NonNull Result result) {
                runOnUiThread(new Runnable() {
                    @Override
                    public void run() {
                        String uid = result.getText().toString();
                        JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(
                                Request.Method.GET,
                                getInfoUrl(uid),
                                null,
                                new Response.Listener<JSONObject>() {
                                    @Override
                                    public void onResponse(JSONObject response) {
                                        try {
                                            if (response.getBoolean("status") == true) {
                                                String information = response.getJSONObject("data").getString("information");
                                                Intent i = new Intent(ScanQrActivity.this, MainActivity.class);
                                                i.putExtra("information", information);
                                                startActivity(i);
                                            } else {
                                                showToast(response.getJSONArray("emsg").getString(0));
                                            }
                                        } catch (Exception e) {
                                            showToast("Failed to Process Request");
                                        }
                                    }
                                },
                                new Response.ErrorListener() {
                                    @Override
                                    public void onErrorResponse(VolleyError error) {
                                        Log.e(TAG, "onErrorResponse: " + error.toString());
                                    }
                                }
                        );
                        requestQueue.add(jsonObjectRequest);
                    }
                });
            }
        });
        codeScanner.startPreview();

        codeScannerView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                codeScanner.startPreview();
            }
        });
    }

    private String getInfoUrl(String uid) {
        Uri loginUri = Uri.parse(getString(R.string.API_ENDPOINT) + "informations.php")
                .buildUpon()
                .appendQueryParameter("view", "1")
                .appendQueryParameter("uid", uid)
                .appendQueryParameter("u_id", String.valueOf(u_id))
                .appendQueryParameter("latitude", latitude)
                .appendQueryParameter("longitude", longitude)
                .build();

        return loginUri.toString();
    }

    private void getLocation() {
        if (ActivityCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION) == PackageManager.PERMISSION_GRANTED) {

            String provider = locationManager.getBestProvider(new Criteria(), true);
            Location location = locationManager.getLastKnownLocation(provider);
            if (location == null) {
                showToast("Sorry!, Error");
            } else {
                latitude = String.valueOf(location.getLatitude());
                longitude = String.valueOf(location.getLongitude());
                Log.d(TAG, "getLocation: " + provider + " " + latitude + " " + longitude);
            }
        } else {
        }
    }

    private void verifyLogin() {
        u_id = sharedPreferences.getInt("u_id", 0);
        if (u_id == 0) {
            Intent i = new Intent(ScanQrActivity.this, LoginActivity.class);
            startActivity(i);
        }
    }

    private void verifyGpsStatus() {

    }

    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
    }

    private void showToast(String s) {
        Toast.makeText(getApplicationContext(), s, Toast.LENGTH_SHORT).show();
    }
}